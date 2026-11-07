<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
$routes=[
 'home'=>['PaginaController','home'], 'portfolio'=>['PaginaController','portfolio'], 'temporadas'=>['PaginaController','portfolio'],
 'register'=>['RegisterController','index'], 'register_add'=>['RegisterController','add'],
 'auth_login'=>['AuthController','login'], 'auth_authenticate'=>['AuthController','authenticate'], 'auth_logout'=>['AuthController','logout'],
 'contact'=>['ContactController','index'], 'contact_send'=>['ContactController','send'],
 'profile'=>['ProfileController','index'], 'profile_save'=>['ProfileController','save'], 'profile_photo'=>['ProfileController','photo'],
 'password_forgot'=>['PasswordResetController','forgot'], 'password_request'=>['PasswordResetController','request'], 'password_reset'=>['PasswordResetController','reset'], 'password_update'=>['PasswordResetController','update'],
 'admin'=>['AdminController','index'], 'admin_users'=>['AdminController','users'], 'admin_edit'=>['AdminController','edit'], 'admin_permission'=>['AdminController','permission'],
 'admin_messages'=>['AdminController','messages'], 'admin_status'=>['AdminController','status'], 'admin_audit'=>['AdminController','audit'],
];
try {
 $route=$_GET['route'] ?? 'home';
 $target=is_string($route) ? ($routes[$route] ?? null) : null;
 if (!$target || !class_exists($target[0]) || !method_exists($target[0],$target[1])) {
     (new PaginaController())->index();
 } else { (new $target[0]())->{$target[1]}(); }
} catch (Throwable $ex) {
 log_failure($ex); http_response_code(503);
 // Layout de erro sem acesso adicional ao banco.
 $safeError=true;
 render_view('error',['safeError'=>true,'title'=>'O portal está instável','message'=>'Não foi possível concluir agora. Tente novamente em alguns instantes. Se o problema continuar, avise o responsável pelo site.']);
}
