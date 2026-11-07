<?php
// Testes de helpers que não precisam do banco. Não execute este arquivo pela web.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/config.php';
function check(bool $ok,string $name): void { if(!$ok){fwrite(STDERR,"FALHOU: $name\n");exit(1);}echo "PASSOU: $name\n"; }
check(e('<script>"\'&')==='&lt;script&gt;&quot;&#039;&amp;','escape XSS');
check(!class_exists('../../etc/passwd'),'autoload rejeita caminhos');
check(!class_exists('ControllerInexistente'),'autoload rejeita classe inexistente');
check(strlen(csrf())===64 && hash_equals(csrf(),csrf()),'CSRF consistente');
check(strpos(url('auth_login'),'index.php?route=auth_login')!==false,'URL do login');
check(!password_ok('123456789') && password_ok('1234567890') && !password_ok('ááááááááá'),'tamanho real da senha');
$password='senha-de-teste-123';$hash=password_hash($password,PASSWORD_DEFAULT);
check(password_verify($password,$hash) && !password_verify('incorreta',$hash),'hash de senha');
$data=['viewName'=>'../../etc/passwd','layout'=>null];ob_start();render_view('404',$data,null);$html=ob_get_clean();
check(strpos($html,'Você foi')!==false && strpos($html,'root:x')===false,'extract não sobrescreve view e layout');
