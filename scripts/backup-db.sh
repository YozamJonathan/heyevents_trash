#!/usr/bin/env bash
# Take a compressed, restorable MySQL dump.
#
#   ./scripts/backup-db.sh          # -> backups/heyevent-20260928-120000.sql.gz
#
# Set AWS_BUCKET in .env.production to also push a copy to S3, which is the only
# backup that survives losing the instance. The 5 GB S3 allowance is free on
# every account, so there is no reason not to.
#
# Cron (host crontab), 03:17 daily — an odd minute so this does not collide
# with every other job on the box:
#   17 3 * * * /full/path/to/heyevent/scripts/backup-db.sh >> /var/log/heyevent-backup.log 2>&1
set -euo pipefail

cd "$(dirname "$0")/.."

ENV_FILE=".env.production"
BACKUP_DIR="backups"
KEEP_LOCAL=7

[ -f "$ENV_FILE" ] || { echo "error: $ENV_FILE not found" >&2; exit 1; }

get_env() { sed -n "s/^$1=//p" "$ENV_FILE" | head -n1; }

DB_DATABASE=$(get_env DB_DATABASE)
DB_ROOT_PASSWORD=$(get_env DB_ROOT_PASSWORD)
AWS_BUCKET=$(get_env AWS_BUCKET)

[ -n "$DB_DATABASE" ] || { echo "error: DB_DATABASE is empty in $ENV_FILE" >&2; exit 1; }
[ -n "$DB_ROOT_PASSWORD" ] || { echo "error: DB_ROOT_PASSWORD is empty in $ENV_FILE" >&2; exit 1; }

COMPOSE=(docker compose --env-file "$ENV_FILE")
mkdir -p "$BACKUP_DIR"

stamp=$(date -u +%Y%m%d-%H%M%S)
out="$BACKUP_DIR/${DB_DATABASE}-${stamp}.sql.gz"

echo "==> dumping $DB_DATABASE to $out"

# --single-transaction gives a consistent snapshot on InnoDB without locking
#   the site out. --no-tablespaces avoids needing the PROCESS privilege.
"${COMPOSE[@]}" exec -T mysql mysqldump \
    -u root "-p${DB_ROOT_PASSWORD}" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    --events \
    --no-tablespaces \
    --set-gtid-purged=OFF \
    --databases "$DB_DATABASE" \
    | gzip -9 > "$out"

# An empty or truncated dump that "succeeded" is worse than no backup at all,
# so verify the archive really decompresses before trusting it.
if ! gzip -t "$out" 2>/dev/null; then
    echo "error: dump is not valid gzip — discarding $out" >&2
    rm -f "$out"
    exit 1
fi
if [ ! -s "$out" ]; then
    echo "error: dump is empty — discarding $out" >&2
    rm -f "$out"
    exit 1
fi

size=$(du -h "$out" | cut -f1)
echo "    ok: $size"

# --- prune local copies -----------------------------------------------------
shopt -s nullglob
stale=($(ls -1t "$BACKUP_DIR"/*.sql.gz | tail -n +$((KEEP_LOCAL + 1))))
if [ ${#stale[@]} -gt 0 ]; then
    echo "==> pruning ${#stale[@]} backup(s) beyond the newest $KEEP_LOCAL"
    rm -f "${stale[@]}"
fi
shopt -u nullglob

# --- offsite copy -----------------------------------------------------------
if [ -n "$AWS_BUCKET" ]; then
    echo "==> uploading to s3://$AWS_BUCKET/backups/"
    # Run the AWS CLI in a throwaway container so nothing has to be installed
    # on the host.
    if docker run --rm \
        -v "$PWD/$BACKUP_DIR:/backup:ro" \
        -e AWS_ACCESS_KEY_ID -e AWS_SECRET_ACCESS_KEY \
        amazon/aws-cli \
        s3 cp "/backup/$(basename "$out")" \
        "s3://$AWS_BUCKET/backups/$(basename "$out")" \
        --storage-class STANDARD_IA \
        --only-show-errors
    then
        echo "    ok: s3://$AWS_BUCKET/backups/$(basename "$out")"
    else
        echo "    warning: S3 upload failed; the local copy is still intact" >&2
    fi
else
    echo "    note: AWS_BUCKET is empty, so this copy only exists on this disk."
    echo "    Set AWS_BUCKET in $ENV_FILE to get an offsite copy."
fi

echo "==> done"
