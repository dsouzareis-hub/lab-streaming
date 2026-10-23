#!/usr/bin/env bash
# Atualiza o código (git pull), reaplica permissões e migrações pendentes. Uso: sudo scripts/update.sh
set -Eeuo pipefail
[[ $EUID -eq 0 ]] || { echo "Execute como root." >&2; exit 1; }
APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
git config --global --add safe.directory "$APP_DIR" 2>/dev/null || true
git -C "$APP_DIR" pull --ff-only --quiet origin main
DB_NAME="$(grep -E '^DB_NAME=' "$APP_DIR/config/.env" | cut -d= -f2-)"
for f in "$APP_DIR"/database/migrations/*.sql; do
  n="$(basename "$f")"
  if [[ -z "$(mariadb -N "$DB_NAME" -e "SELECT 1 FROM schema_migrations WHERE name='${n}'")" ]]; then
    mariadb "$DB_NAME" < "$f" && mariadb "$DB_NAME" -e "INSERT INTO schema_migrations (name) VALUES ('${n}')"
    echo "migração aplicada: ${n}"
  fi
done
chown -R root:www-data "$APP_DIR"
find "$APP_DIR" -path "$APP_DIR/.git" -prune -o -type d -exec chmod 750 {} +
find "$APP_DIR" -path "$APP_DIR/.git" -prune -o -type f -exec chmod 640 {} +
chmod 750 "$APP_DIR"/install.sh "$APP_DIR"/scripts/*.sh
for d in storage storage/logs storage/cache storage/mail public/uploads; do
  chown -R www-data:www-data "$APP_DIR/$d"; chmod 750 "$APP_DIR/$d"
done
if id -u ksstatus &>/dev/null; then
  setfacl -m u:ksstatus:rx "$APP_DIR"
  setfacl -m u:ksstatus:rx "$APP_DIR/scripts"
  setfacl -R -m u:ksstatus:rx "$APP_DIR/scripts/internal-status"
  setfacl -d -m u:ksstatus:rx "$APP_DIR/scripts/internal-status"
fi
systemctl reload apache2
echo "Atualizado para $(git -C "$APP_DIR" rev-parse --short HEAD)"
