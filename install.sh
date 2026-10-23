#!/usr/bin/env bash
# Kids Stream - instalador idempotente para Ubuntu Server (testado em 26.04 LTS).
# Uso:  sudo ./install.sh        (ou)   curl/git clone do repositório e executar como root.
# Variáveis opcionais: REPO_URL, BRANCH, APP_DIR, SSH_ALLOW_FROM (CIDR permitido no SSH; padrão: qualquer origem).
set -Eeuo pipefail

REPO_URL="${REPO_URL:-https://github.com/dsouzareis-hub/lab-streaming.git}"
BRANCH="${BRANCH:-main}"
APP_DIR="${APP_DIR:-/opt/kidsstream}"
DB_NAME="${DB_NAME:-kidsstream}"
DB_USER="${DB_USER:-kidsstream_app}"
SSH_ALLOW_FROM="${SSH_ALLOW_FROM:-any}"
CRED_FILE="/root/kidsstream-credentials.txt"
export DEBIAN_FRONTEND=noninteractive

c_ok=$'\e[32m'; c_err=$'\e[31m'; c_inf=$'\e[36m'; c_off=$'\e[0m'
step() { echo "${c_inf}==>${c_off} $*"; }
ok()   { echo "${c_ok}  ok${c_off} $*"; }
die()  { echo "${c_err}ERRO:${c_off} $*" >&2; exit 1; }
trap 'echo "${c_err}Falha na linha $LINENO. Corrija e execute novamente (o instalador é idempotente).${c_off}" >&2' ERR

[[ $EUID -eq 0 ]] || die "Execute como root (sudo ./install.sh)."

# ---------- 1. Verificações de ambiente ----------
step "Verificando sistema operacional e requisitos"
. /etc/os-release
[[ "${ID:-}" == "ubuntu" ]] || die "Sistema não suportado: ${PRETTY_NAME:-desconhecido} (esperado Ubuntu)."
[[ "$(uname -m)" == "x86_64" || "$(uname -m)" == "aarch64" ]] || die "Arquitetura não suportada."
FREE_MB=$(df -Pm / | awk 'NR==2{print $4}')
(( FREE_MB >= 2048 )) || die "Espaço livre insuficiente (${FREE_MB} MB; mínimo 2048 MB)."
ok "${PRETTY_NAME} / $(uname -m) / ${FREE_MB} MB livres"
if [[ "${VERSION_ID:-}" == "25.04" ]]; then
  echo "AVISO: Ubuntu 25.04 é versão intermediária sem suporte (EOL). Considere uma versão LTS." >&2
fi

step "Verificando repositórios de pacotes"
apt-get update -qq || die "Não foi possível atualizar a lista de pacotes (verifique a conectividade)."
ok "apt acessível"

# ---------- 2. Pacotes ----------
step "Instalando dependências"
apt-get install -y -qq --no-install-recommends \
  apache2 libapache2-mod-php php-cli php-mysql php-gd php-mbstring php-xml php-curl \
  mariadb-server git curl openssl ufw fail2ban unattended-upgrades ca-certificates >/dev/null
ok "pacotes instalados"
PHPV="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
ok "PHP ${PHPV}, $(apache2 -v | awk 'NR==1{print $3}'), $(mariadb --version | awk '{print $5}' | tr -d ,)"

# ---------- 3. Código ----------
step "Obtendo o código em ${APP_DIR}"
if [[ -d "${APP_DIR}/.git" ]]; then
  git -C "$APP_DIR" fetch --quiet origin "$BRANCH"
  git -C "$APP_DIR" merge --ff-only --quiet "origin/${BRANCH}" || die "Há alterações locais divergentes em ${APP_DIR}; resolva manualmente."
else
  [[ ! -e "$APP_DIR" || -z "$(ls -A "$APP_DIR" 2>/dev/null)" ]] || die "${APP_DIR} existe e não é um clone git."
  git clone --quiet --branch "$BRANCH" "$REPO_URL" "$APP_DIR"
fi
ok "código em $(git -C "$APP_DIR" rev-parse --short HEAD)"

# ---------- 4. Configuração (.env) ----------
step "Configurando credenciais"
ENV_FILE="${APP_DIR}/config/.env"
if [[ ! -f "$ENV_FILE" ]]; then
  DB_PASS="$(openssl rand -hex 20)"
  umask 077
  cat > "$ENV_FILE" <<EOF
APP_NAME="Kids Stream"
APP_ENV=production
APP_DEBUG=0
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
DB_PASS=${DB_PASS}
EOF
  ok ".env criado com senha de banco aleatória"
else
  DB_PASS="$(grep -E '^DB_PASS=' "$ENV_FILE" | cut -d= -f2-)"
  ok ".env existente preservado"
fi

# ---------- 5. Banco de dados ----------
step "Preparando MariaDB"
install -m 644 "${APP_DIR}/deploy/mariadb-kidsstream.cnf" /etc/mysql/mariadb.conf.d/99-kidsstream.cnf
systemctl enable --now mariadb >/dev/null 2>&1
systemctl restart mariadb
for _ in $(seq 1 30); do mariadb -e 'SELECT 1' >/dev/null 2>&1 && break; sleep 1; done
mariadb -e 'SELECT 1' >/dev/null 2>&1 || die "MariaDB não respondeu."
mariadb <<SQL
DELETE FROM mysql.global_priv WHERE User='';
DROP DATABASE IF EXISTS test;
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT SELECT,INSERT,UPDATE,DELETE ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
GRANT SELECT,INSERT,UPDATE,DELETE ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
mariadb "$DB_NAME" -e "CREATE TABLE IF NOT EXISTS schema_migrations (name VARCHAR(190) PRIMARY KEY, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB"
for f in "${APP_DIR}"/database/migrations/*.sql; do
  n="$(basename "$f")"
  if [[ -z "$(mariadb -N "$DB_NAME" -e "SELECT 1 FROM schema_migrations WHERE name='${n}'")" ]]; then
    mariadb "$DB_NAME" < "$f"
    mariadb "$DB_NAME" -e "INSERT INTO schema_migrations (name) VALUES ('${n}')"
    ok "migração aplicada: ${n}"
  fi
done

# ---------- 6. Permissões ----------
step "Aplicando permissões"
chown -R root:www-data "$APP_DIR"
find "$APP_DIR" -path "$APP_DIR/.git" -prune -o -type d -exec chmod 750 {} +
find "$APP_DIR" -path "$APP_DIR/.git" -prune -o -type f -exec chmod 640 {} +
chmod 750 "$APP_DIR"/install.sh "$APP_DIR"/scripts/*.sh 2>/dev/null || true
chmod 640 "$ENV_FILE"
for d in storage storage/logs storage/cache storage/mail public/uploads; do
  mkdir -p "${APP_DIR}/${d}"
  chown -R www-data:www-data "${APP_DIR}/${d}"
  chmod 750 "${APP_DIR}/${d}"
done
ok "código root:www-data (somente leitura); storage e uploads graváveis pelo Apache"

# ---------- 7. Seed ----------
step "Carregando dados fictícios"
php "${APP_DIR}/scripts/seed.php" "$CRED_FILE"
[[ -f "$CRED_FILE" ]] && chmod 600 "$CRED_FILE"

# ---------- 8. Apache / PHP ----------
step "Configurando Apache e PHP"
sed "s#@APP_DIR@#${APP_DIR}#g" "${APP_DIR}/deploy/kidsstream.conf" > /etc/apache2/sites-available/kidsstream.conf
install -m 644 "${APP_DIR}/deploy/security.conf" /etc/apache2/conf-available/kidsstream-security.conf
sed "s#@APP_DIR@#${APP_DIR}#g" "${APP_DIR}/deploy/99-kidsstream.ini" > "/etc/php/${PHPV}/apache2/conf.d/99-kidsstream.ini"
a2enmod -q headers >/dev/null
a2dismod -q -f status autoindex >/dev/null 2>&1 || true
a2enconf -q kidsstream-security
a2dissite -q 000-default >/dev/null 2>&1 || true
a2ensite -q kidsstream
apache2ctl configtest >/dev/null 2>&1 || { apache2ctl configtest; die "Configuração do Apache inválida."; }
systemctl enable apache2 >/dev/null 2>&1
systemctl restart apache2
ok "Apache ativo"

# ---------- 9. Resiliência e inicialização ----------
step "Configurando recuperação automática e aviso de URL"
for s in apache2 mariadb; do
  mkdir -p "/etc/systemd/system/${s}.service.d"
  printf '[Service]\nRestart=on-failure\nRestartSec=5\n' > "/etc/systemd/system/${s}.service.d/restart.conf"
done
mkdir -p /etc/systemd/system/apache2.service.d
printf '[Unit]\nAfter=mariadb.service\nWants=mariadb.service\n' > /etc/systemd/system/apache2.service.d/order.conf
install -m 755 "${APP_DIR}/scripts/show-url.sh" /usr/local/bin/kidsstream-url
install -m 644 "${APP_DIR}/deploy/kidsstream-url.service" /etc/systemd/system/kidsstream-url.service
systemctl daemon-reload
systemctl enable kidsstream-url.service >/dev/null 2>&1
systemctl start kidsstream-url.service || true
ok "serviços reiniciam em falha; URL exibida no console a cada boot (comando: kidsstream-url)"

# ---------- 10. Firewall, SSH e proteção ----------
step "Aplicando firewall e proteção contra força bruta"
ufw --force reset >/dev/null
ufw default deny incoming >/dev/null
ufw default allow outgoing >/dev/null
if [[ "$SSH_ALLOW_FROM" == "any" ]]; then ufw allow 22/tcp comment 'SSH' >/dev/null
else ufw allow from "$SSH_ALLOW_FROM" to any port 22 proto tcp comment 'SSH admin' >/dev/null; fi
ufw allow 80/tcp comment 'Kids Stream HTTP' >/dev/null
# Trava de segurança: só ativa se as regras de SSH e HTTP estiverem de fato registradas
ufw show added | grep -qE '22/tcp|port 22' || die "Regra de SSH não registrada no UFW; firewall NÃO ativado (evita perder o acesso)."
ufw show added | grep -q 'allow 80/tcp' || die "Regra HTTP não registrada no UFW; firewall NÃO ativado."
# Reversão automática em 5 min caso o acesso seja perdido; cancelada abaixo após validar o SSH/HTTP
systemctl stop kidsstream-ufw-rollback.timer >/dev/null 2>&1 || true
systemd-run --quiet --unit=kidsstream-ufw-rollback --on-active=300 /usr/sbin/ufw --force disable
ufw --force enable >/dev/null
ufw status | grep -qE '^22/tcp|^22 ' || die "Regra de SSH ausente após ativação."
ok "UFW ativo: 22/tcp (${SSH_ALLOW_FROM}) e 80/tcp"

ADMIN_IP="${SSH_CLIENT%% *}"
install -d /etc/fail2ban/jail.d
cat > /etc/fail2ban/jail.d/kidsstream.local <<EOF
[DEFAULT]
ignoreip = 127.0.0.1/8 ::1 ${ADMIN_IP:-}
[sshd]
enabled = true
maxretry = 5
findtime = 10m
bantime = 15m
[apache-badbots]
enabled = false
EOF
systemctl enable --now fail2ban >/dev/null 2>&1
systemctl restart fail2ban
systemctl enable --now unattended-upgrades >/dev/null 2>&1 || true
ok "fail2ban (sshd) e atualizações automáticas de segurança ativos"

# ---------- 11. Testes de saúde ----------
step "Executando testes de saúde"
FAIL=0
check() { if eval "$2" >/dev/null 2>&1; then ok "$1"; else echo "${c_err}  falhou${c_off} $1"; FAIL=1; fi; }
check "MariaDB ativo"            "systemctl is-active --quiet mariadb"
check "Apache ativo"             "systemctl is-active --quiet apache2"
check "MariaDB só em loopback"   "! ss -ltn | grep -qE '(0\\.0\\.0\\.0|\\*|\\[::\\]):3306[[:space:]]'"
check "Aplicação conecta ao BD"  "php -r 'require \"${APP_DIR}/src/bootstrap.php\"; Db::one(\"SELECT COUNT(*) c FROM movies\");'"
check "HTTP / responde 200"      "curl -fsS -o /dev/null http://127.0.0.1/"
check "HTTP /login responde 200" "curl -fsS -o /dev/null http://127.0.0.1/login"
check "/config não exposto"      "[[ \$(curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1/config/.env) != 200 ]]"
check "UFW ativo"                "ufw status | grep -q 'Status: active'"

if (( FAIL == 0 )); then
  systemctl stop kidsstream-ufw-rollback.timer >/dev/null 2>&1 || true
  ok "reversão automática do firewall cancelada"
else
  echo "${c_err}Falhas detectadas: o firewall será desativado automaticamente em até 5 min (rollback de segurança).${c_off}"
fi

IPS="$(hostname -I 2>/dev/null | xargs)"
echo
if (( FAIL == 0 )); then echo "${c_ok}INSTALAÇÃO CONCLUÍDA COM SUCESSO${c_off}"; else echo "${c_err}INSTALAÇÃO CONCLUÍDA COM FALHAS (veja acima)${c_off}"; fi
for ip in $IPS; do echo "  Acesse: http://${ip}/"; done
echo "  Credenciais das contas de demonstração: ${CRED_FILE} (somente root)"
echo "  Para rever a URL a qualquer momento: kidsstream-url"
exit "$FAIL"
