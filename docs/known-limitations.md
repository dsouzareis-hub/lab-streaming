# Limitações conhecidas

- **Sistema operacional:** o documento de referência deste laboratório citava Ubuntu Server
  25.04 (Plucky Puffin), uma versão intermediária sem suporte de longo prazo. A VM de destino
  deste laboratório já veio provisionada com **Ubuntu Server 26.04 LTS**, que é a versão
  recomendada (suportada e com atualizações de segurança) e foi mantida como está — o `install.sh`
  verifica a versão e apenas emite um aviso, sem bloquear, caso detecte 25.04.
- **HTTPS:** não configurado. O cenário roda em rede local sem nome de domínio público nem
  autoridade certificadora disponível; a aplicação serve apenas HTTP (porta 80), adequado para um
  laboratório isolado.
- **E-mail transacional:** não há servidor SMTP configurado. Os fluxos que normalmente enviariam
  e-mail (recuperação de senha) gravam o conteúdo em `storage/mail/` como arquivo local, para fins
  de demonstração.
- **AppArmor:** o Ubuntu já traz o AppArmor ativo por padrão para diversos pacotes do sistema; não
  foram autorados perfis customizados específicos para Apache/PHP neste projeto (ficou fora do
  escopo de tempo). O isolamento do serviço de laboratório da vulnerabilidade de Exposed Services é
  feito via `systemd` (usuário dedicado sem privilégios, `ProtectSystem=strict`,
  `NoNewPrivileges=true` etc.), não via AppArmor.
- **JIT do PCRE:** o log do PHP pode registrar o aviso "Allocation of JIT memory failed... This is
  likely caused by security restrictions". É uma característica do kernel/ambiente desta VM (não
  introduzida por este projeto); o PCRE volta a funcionar sem JIT (mais lento, mas correto) e isso
  não afeta a segurança nem a funcionalidade da aplicação.
- **Disponibilidade dos trailers:** depende da conectividade com a internet do navegador do usuário
  e das políticas do YouTube; vídeos podem ser removidos ou ter a incorporação desativada a
  qualquer momento (ver `scripts/check_videos.php` para revalidar o catálogo).
