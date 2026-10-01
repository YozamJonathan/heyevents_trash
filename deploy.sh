#!/usr/bin/env bash
# Build and deploy HeyEvent.
#
#   ./deploy.sh                      # auto-detects the public URL from AWS
#   ./deploy.sh https://events.example.com
#
# Safe to re-run. Every step is idempotent, so this doubles as "apply the
# config change I just made" without a separate command.
set -euo pipefail

cd "$(dirname "$0")"

ENV_FILE=".env.production"

info()  { printf '\n\033[1;34m==>\033[0m %s\n' "$*"; }
warn()  { printf '\033[1;33mwarning:\033[0m %s\n' "$*" >&2; }
die()   { printf '\033[1;31merror:\033[0m %s\n' "$*" >&2; exit 1; }

# --- helpers ----------------------------------------------------------------

get_env() { sed -n "s/^$1=//p" "$ENV_FILE" | head -n1; }

set_env() {
    local key="$1" val="$2" esc
    esc=$(printf '%s' "$val" | sed -e 's/[&|]/\\&/g')
    if grep -qE "^${key}=" "$ENV_FILE"; then
        sed -i "s|^${key}=.*$|${key}=${esc}|" "$ENV_FILE"
    else
        printf '%s=%s\n' "$key" "$val" >> "$ENV_FILE"
    fi
}

rand_alnum() { LC_ALL=C tr -dc 'A-Za-z0-9' < /dev/urandom | head -c "$1"; }

# AWS instance metadata, IMDSv2 first. Returns non-zero off-AWS or on a
# hardened IMDS endpoint.
detect_public_ip() {
    local token ip
    token=$(curl -s -m 2 -X PUT "http://169.254.169.254/latest/api/token" \
        -H "X-aws-ec2-metadata-token-ttl-seconds: 60" 2>/dev/null) || token=""
    if [ -n "$token" ]; then
        ip=$(curl -s -m 2 -H "X-aws-ec2-metadata-token: $token" \
            http://169.254.169.254/latest/meta-data/public-ipv4 2>/dev/null) || ip=""
        if [ -n "$ip" ]; then printf '%s' "$ip"; return 0; fi
    fi
    ip=$(curl -fsS -m 4 https://checkip.amazonaws.com 2>/dev/null) || ip=""
    if [ -n "$ip" ]; then printf '%s' "$ip" | tr -d '[:space:]'; return 0; fi
    return 1
}

# --- preflight --------------------------------------------------------------

info "Preflight"
command -v docker >/dev/null 2>&1 || die "docker is not installed"
docker info >/dev/null 2>&1 || die "cannot reach the Docker daemon — is your user in the 'docker' group?"

# Locate Compose. The v2 plugin is the normal case; the standalone v1 binary is
# accepted as a fallback so this still runs on older hosts.
if docker compose version >/dev/null 2>&1; then
    COMPOSE_CMD=(docker compose)
elif command -v docker-compose >/dev/null 2>&1 && docker-compose version >/dev/null 2>&1; then
    COMPOSE_CMD=(docker-compose)
else
    die "Docker Compose was not found.
  On Ubuntu/Debian:  sudo apt-get install -y docker-compose-plugin
  Full instructions: https://docs.docker.com/compose/install/"
fi

# The compose file interpolates \${DB_*} from .env.production rather than a
# project-root .env, so --env-file support is required. Probe for it up front so
# a missing flag fails loudly instead of silently blanking the MySQL password.
if ! "${COMPOSE_CMD[@]}" --env-file /dev/null version >/dev/null 2>&1; then
    die "'${COMPOSE_CMD[*]}' does not support --env-file. Upgrade Docker Compose."
fi

COMPOSE=("${COMPOSE_CMD[@]}" --env-file "$ENV_FILE")

[ -f Dockerfile ] || die "Dockerfile not found; run this from the project root"
[ -f docker-compose.yml ] || die "docker-compose.yml not found"

# --- environment file -------------------------------------------------------

if [ ! -f "$ENV_FILE" ]; then
    info "Creating $ENV_FILE from the example"
    [ -f .env.production.example ] || die ".env.production.example is missing"
    cp .env.production.example "$ENV_FILE"
    chmod 600 "$ENV_FILE"
fi

info "Ensuring required values are set"

if [ -z "$(get_env APP_KEY)" ]; then
    set_env APP_KEY "base64:$(head -c 32 /dev/urandom | base64 -w0)"
    info "  generated APP_KEY"
fi

if [ -z "$(get_env DB_PASSWORD)" ]; then
    set_env DB_PASSWORD "$(rand_alnum 28)"
    info "  generated DB_PASSWORD"
fi

if [ -z "$(get_env DB_ROOT_PASSWORD)" ]; then
    set_env DB_ROOT_PASSWORD "$(rand_alnum 32)"
    info "  generated DB_ROOT_PASSWORD"
fi

if [ -z "$(get_env APP_URL)" ]; then
    if [ -n "${1:-}" ]; then
        set_env APP_URL "${1%/}"
        info "  set APP_URL from argument"
    elif ip=$(detect_public_ip); then
        set_env APP_URL "http://$ip"
        info "  set APP_URL from detected public IP ($ip)"
    else
        die "could not detect a public IP. Re-run as: ./deploy.sh https://your-domain-or-ip"
    fi
fi

for key in APP_KEY APP_URL DB_DATABASE DB_USERNAME DB_PASSWORD DB_ROOT_PASSWORD; do
    [ -n "$(get_env "$key")" ] || die "$key is empty in $ENV_FILE — fill it in manually"
done
case "$(get_env APP_URL)" in
    http://*|https://*) ;;
    *) die "APP_URL must start with http:// or https:// (got '$(get_env APP_URL)')" ;;
esac

# --- build and start --------------------------------------------------------

info "Building the image"
"${COMPOSE[@]}" build

info "Starting the stack"
"${COMPOSE[@]}" up -d --remove-orphans

info "Waiting for the app container to report healthy"
app_cid=$("${COMPOSE[@]}" ps -q app)
[ -n "$app_cid" ] || die "the app container did not start"

for i in $(seq 1 60); do
    status=$(docker inspect -f '{{if .State.Health}}{{.State.Health.Status}}{{else}}none{{end}}' "$app_cid" 2>/dev/null || echo none)
    case "$status" in
        healthy) info "  healthy"; break ;;
        unhealthy) "${COMPOSE[@]}" logs --tail=60 app; die "the app container is unhealthy" ;;
    esac
    if [ "$i" -eq 60 ]; then "${COMPOSE[@]}" logs --tail=60 app; die "timed out waiting for the app"; fi
    sleep 2
done

# --- application setup ------------------------------------------------------

info "Running database migrations"
"${COMPOSE[@]}" exec -T app php artisan migrate --force --no-interaction

info "Linking public/storage"
"${COMPOSE[@]}" exec -T app php artisan storage:link --force >/dev/null

info "Caching config, routes and views"
"${COMPOSE[@]}" exec -T app php artisan config:cache
"${COMPOSE[@]}" exec -T app php artisan route:cache
"${COMPOSE[@]}" exec -T app php artisan view:cache

info "Restarting queue workers"
"${COMPOSE[@]}" exec -T app php artisan queue:restart

# --- done -------------------------------------------------------------------

url=$(get_env APP_URL)
printf '\n\033[1;32mDeployed.\033[0m  %s\n' "$url"
printf 'Logs:     docker compose --env-file %s logs -f app\n' "$ENV_FILE"
printf 'Shell:    docker compose --env-file %s exec app bash\n' "$ENV_FILE"
printf 'Backups:  ./scripts/backup-db.sh\n\n'
