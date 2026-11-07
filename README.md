# Mundo Invertido

Projeto acadêmico de fã inspirado em Stranger Things, desenvolvido em PHP com MVC e MySQL/MariaDB. Inclui páginas públicas, temporadas, cadastro, login, perfil, contato e administração.

## Requisitos

- PHP 8.1 ou superior com mysqli/mysqlnd, fileinfo e sessões.
- MySQL 5.7+ ou MariaDB 10.4+.
- Apache com mod_rewrite e AllowOverride para aplicar as proteções de `.htaccess`.

## Instalação local

1. Coloque o projeto na pasta pública do XAMPP, por exemplo `htdocs/projinvertido`.
2. Importe `database/schema.sql` em uma instalação nova. Faça backup antes de importar em um banco existente.
3. Copie `config.local.example.php` para `config.local.php` e configure o banco e `APP_BASE_URL` no seu computador. O arquivo local é ignorado pelo Git.
4. Permita escrita ao usuário do Apache apenas em `log/` e leitura dos demais arquivos.
5. Abra `http://localhost/projinvertido/`.

Não há contas ou credenciais incluídas. Para promover uma conta cadastrada a administrador, execute no terminal `php bin/create-admin.php` seguido do endereço da conta.

A recuperação de acesso usa um arquivo privado em `log/` no modo local. O envio real depende da configuração de transporte no servidor. Não publique arquivos locais de configuração ou logs.

## Verificação

```bash
php tests/smoke.php
php -S 127.0.0.1:8080 -t . router.php
# Em outro terminal:
python3 tests/http-smoke.py http://127.0.0.1:8080
```

O servidor embutido serve para desenvolvimento. Para hospedagem, configure HTTPS, banco, transporte de mensagens e permissões no servidor e verifique os fluxos autenticados e as regras do Apache.

## Créditos

Base: Semana Front-end Mundo Invertido / DIO, de Michele Queiroz Ambrosio, Diogo Medeiros Mainardes e Renan Johannsen de Paula. Stranger Things e seus recursos pertencem aos respectivos titulares; este projeto não tem vínculo com a Netflix. Créditos das imagens estão nos arquivos de origem em `views/assets/img/`. Fontes Open Sans acompanham a licença em `views/assets/fonts/LICENSE.txt`. GSAP é distribuído com o aviso de licença no arquivo da biblioteca.
