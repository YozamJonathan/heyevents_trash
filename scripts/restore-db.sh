#!/usr/bin/env bash
# Restore a dump produced by scripts/backup-db.sh.
#
#   ./scripts/restore-db.sh backups/heyevent-20260928-120000.sql.gz
#
# This OVERWRITES the current contents of the database. It asks first unless
# you pass --yes.
set -euo pipefail

cd "$(dirname "$0")/.."

ENV_FILE=".env.production"
ASSUME_YES=0
file=""

for arg in "$@"; do
    case "$arg" in
        --yes|-y) ASSUME_YES=1 ;;
        -*) echo "error: unknown option $arg" >&2; exit 1 ;;
        *) file="$arg" ;;
    esac
done

[ -n "$file" ] || { echo "usage: $0 <backup.sql.gz> [--yes]" >&2; exit 1; }
[ -f "$file" ] || { echo "error: $file not found" >&2; exit 1; }
[ -f "$ENV_FILE" ] || { echo "error: $ENV_FILE not found" >&2; exit 1; }

get_env() { sed -n "s/^$1=//p" "$ENV_FILE" | head -n1; }

DB_DATABASE=$(get_env DB_DATABASE)
DB_ROOT_PASSWORD=$(get_env DB_ROOT_PASSWORD)
[ -n "$DB_DATABASE" ] && [ -n "$DB_ROOT_PASSWORD" ] || {
    echo "error: DB_DATABASE / DB_ROOT_PASSWORD missing from $ENV_FILE" >&2; exit 1; }

COMPOSE=(docker compose --env-file "$ENV_FILE")

if [ "$ASSUME_YES" -ne 1 ]; then
    echo "About to overwrite the '${DB_DATABASE}' database using:"
    echo "  $file"
    printf 'Type the database name to confirm: '
    read -r answer
    if [ "$answer" != "$DB_DATABASE" ]; then
        echo "aborted"
        exit 1
    fi
fi

echo "==> stopping the app so nothing writes mid-restore"
"${COMPOSE[@]}" stop app queue

echo "==> restoring"
gunzip -c "$file" \
    | "${COMPOSE[@]}" exec -T mysql mysql -u root "-p${DB_ROOT_PASSWORD}"

echo "==> starting the app"
"${COMPOSE[@]}" start app queue

echo "==> verifying the schema is present"
"${COMPOSE[@]}" exec -T app php artisan migrate:status | tail -n 5

echo "==> done"
