#!/usr/bin/env bash
# Kids Stream - relatorio periodico de uso (executado por root via cron, ver deploy/kidsstream-report.cron).
#
# Vulnerabilidade intencional 4 - Cron Job Exploitation.
# ATENCAO (laboratorio): na implantacao (install.sh), este arquivo e copiado para fora do
# repositorio com propriedade root:ksweb e permissao 0775 -- o GRUPO "ksweb" tem permissao de
# ESCRITA sobre um script executado por root a cada 5 minutos. Qualquer membro do grupo ksweb
# pode alterar o conteudo abaixo e obter execucao de codigo como root na proxima execucao.
#
# Gabarito completo: docs/private/vuln-04-cron.md (fora do repositorio publico).
set -euo pipefail
echo "[$(date -Is)] Gerando relatorio de uso do Kids Stream..."
mariadb kidsstream -N -e "SELECT COUNT(*) FROM movies" 2>/dev/null || true
echo "[$(date -Is)] Relatorio concluido."
