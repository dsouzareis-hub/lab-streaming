# Kids Stream

Plataforma fictícia de streaming de trailers de filmes infantis (PHP + Apache + MariaDB), criada como laboratório para uma competição de Cyber Security.

> Todo o conteúdo e todos os dados são fictícios. Use somente em ambiente isolado e autorizado.

## Instalação (Ubuntu Server, como root)

```bash
sudo apt-get update && sudo apt-get install -y git
git clone https://github.com/dsouzareis-hub/lab-streaming.git /opt/kidsstream
sudo /opt/kidsstream/install.sh
```

O instalador é idempotente: pode ser executado novamente para atualizar o código (`git pull`), reaplicar migrações e permissões.
Variáveis opcionais: `SSH_ALLOW_FROM=<CIDR>` (restringe o SSH), `REPO_URL`, `BRANCH`, `APP_DIR`.

Ao final ele exibe a URL de acesso. Após mudar de rede, rode `kidsstream-url` (a URL também aparece no banner do console a cada boot).
As senhas das contas de demonstração são geradas aleatoriamente e gravadas em `/root/kidsstream-credentials.txt` (somente root).

## Estrutura

| Pasta | Conteúdo |
|---|---|
| `public/` | Única raiz exposta pelo Apache (front controller e assets) |
| `src/` | Núcleo: sessão, CSRF, autenticação/autorização, banco, validação |
| `pages/`, `templates/` | Controladores e views (fora da raiz pública) |
| `database/migrations/` | Esquema do banco |
| `deploy/` | Configurações de Apache, PHP, MariaDB e systemd |
| `scripts/` | Seed, verificação de vídeos, utilitários |
| `config/.env.example` | Exemplo de configuração (o `.env` real nunca é versionado) |

## Portas previstas no cenário

| Porta | Serviço | Observação |
|---|---|---|
| 22/tcp | SSH administrativo | Acesso restrito conforme `SSH_ALLOW_FROM` |
| 80/tcp | Aplicação Kids Stream (Apache) | Porta pública principal |
| 8089/tcp | Serviço interno de laboratório | Exceção documentada e delimitada — ver `docs/private/vuln-03-exposed-service.md` |
| 3306/tcp | MariaDB | Somente em `127.0.0.1`, nunca exposto à rede |

## Dependências externas

Os trailers usam o player oficial do YouTube (`youtube-nocookie.com`) e as miniaturas vêm de `i.ytimg.com`; é necessária conexão com a internet no navegador dos usuários.
