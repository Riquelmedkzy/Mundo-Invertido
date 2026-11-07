<?php
// Router exclusivo para testes com `php -S`. No Apache use .htaccess.
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/');
if($path==='/' || $path==='/index.php'){require __DIR__.'/index.php';return;}
$target=realpath(__DIR__.$path);
if(str_starts_with($path,'/views/assets/') && $target && str_starts_with($target,__DIR__.'/views/assets/') && is_file($target) && in_array(strtolower(pathinfo($target,PATHINFO_EXTENSION)),['css','js','svg','png','webp','jpg','jpeg','ttf','mp4','mpeg'],true))return false;
http_response_code(404);echo 'Não encontrado.';
