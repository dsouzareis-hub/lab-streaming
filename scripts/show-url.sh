#!/usr/bin/env bash
# Mostra (e grava no banner do console) os endereços de acesso atuais; útil após mudança de rede.
set -u
for _ in $(seq 1 20); do
  IPS="$(hostname -I 2>/dev/null | xargs)"
  [[ -n "$IPS" ]] && break
  sleep 1
done
MSG="Kids Stream disponível em:"
for ip in ${IPS:-}; do MSG+=" http://${ip}/"; done
[[ -z "${IPS:-}" ]] && MSG="Kids Stream: sem endereço IP no momento."
echo "$MSG"
if [[ $EUID -eq 0 && -d /etc/issue.d ]]; then
  printf '%s\n\n' "$MSG" > /etc/issue.d/99-kidsstream.issue
fi
