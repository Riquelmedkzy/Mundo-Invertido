<?php
// Copie para config.local.php e preencha somente no seu computador.
putenv('DB_HOST=127.0.0.1');
putenv('DB_PORT=3306');
putenv('DB_NAME=projinvertido_db');
putenv('DB_USER=root');
putenv('DB_PASSWORD=');
// Necessário para links de recuperação: URL exata da pasta, sem barra final.
putenv('APP_BASE_URL=http://localhost/projinvertido');
// local: grava o link em log/reset-mail.log para demonstração em aula.
// mail: usa mail() do PHP e exige servidor de e-mail configurado.
putenv('MAIL_MODE=local');
putenv('MAIL_FROM=noreply@example.com');
