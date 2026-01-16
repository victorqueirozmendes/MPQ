# Mentoria MPQ

App web mobile-first (PHP + MySQL) para aulas (YouTube), materiais de apoio, novidades e controle de permissoes por usuario.

## Requisitos

- PHP 8.1+ (recomendado)
- MySQL/MariaDB
- Apache com mod_rewrite (Hostinger normalmente usa)

## Setup (local)

1) Crie o banco e importe o schema `database/schema.sql`.

2) Crie um arquivo `.env` na raiz (use `.env.example` como base).

3) Rode o servidor embutido apontando para `public/`:

```bash
cd /home/usuario/Documentos/GitHub/MPQ
php -S 127.0.0.1:8000 -t public
```

Abra `http://127.0.0.1:8000`.

## Criar primeiro admin

Depois de configurar o `.env` e importar o banco:

```bash
php scripts/create_admin.php admin@exemplo.com "SuaSenhaForte"
```

Obs.: por seguranca, senhas sao armazenadas com hash. No painel admin voce consegue resetar senha, nao visualizar a senha atual.

## Deploy (Hostinger)

- Ideal: configurar o Document Root para a pasta `public/`.
- Se nao der para ajustar o Document Root, suba o conteudo de `public/` para `public_html/` e mantenha `src/` + `database/` + `scripts/` fora do webroot (ou proteja com `.htaccess`).
- Garanta permissoes de escrita em:
	- `public/uploads/avatars/`
	- `public/uploads/materials/`
