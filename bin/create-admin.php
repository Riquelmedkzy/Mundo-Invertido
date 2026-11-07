<?php
// Execute pelo terminal. Não existe instalador administrativo público.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/config.php';
if(count($argv)!==2 || !filter_var($argv[1],FILTER_VALIDATE_EMAIL)){fwrite(STDERR,"Uso: php bin/create-admin.php email@exemplo.com\nCadastre a conta no site primeiro.\n");exit(1);}
try {
 $u=(new User())->byEmail($argv[1]);
 if(!$u){fwrite(STDERR,"Conta não encontrada. Cadastre-se no site primeiro.\n");exit(1);}
 if(!(new User())->changeRole((int)$u['id'],(int)$u['id'],'adm'))throw new RuntimeException('Falha ao conceder permissão.');
 echo "Permissão de administrador concedida. Entre novamente no site.\n";
} catch(Throwable $e){log_failure($e);fwrite(STDERR,"Não foi possível acessar o banco. Confira config.local.php e o MySQL.\n");exit(1);}
