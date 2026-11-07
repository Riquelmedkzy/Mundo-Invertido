<?php
declare(strict_types=1);
// Configuração e helpers globais. Não coloque credenciais neste arquivo.
ini_set('display_errors', '0');
define('BASE_PATH', __DIR__);
define('MODELS_PATH', BASE_PATH . '/models');
define('VIEWS_PATH', BASE_PATH . '/views');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');
define('INCLUDES_PATH', VIEWS_PATH . '/includes');
$local = BASE_PATH . '/config.local.php';
if (is_file($local)) require $local;
date_default_timezone_set('America/Sao_Paulo');
define('BASE_URL', rtrim((string)(getenv('APP_BASE_URL') ?: str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'))), '/'));
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/log/erros.log');
if (PHP_SAPI !== 'cli') {
    session_set_cookie_params(['httponly'=>true, 'secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), 'samesite'=>'Lax', 'path'=>'/']);
    ini_set('session.use_strict_mode', '1');
    session_start();
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: DENY');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; media-src 'self'; font-src 'self'; frame-src https://www.youtube-nocookie.com; object-src 'none'; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') header('Strict-Transport-Security: max-age=31536000');
}
spl_autoload_register(function(string $class): void {
    // A rota nunca se torna um caminho de arquivo.
    if (!preg_match('/\A[A-Za-z][A-Za-z0-9]*\z/D', $class)) return;
    foreach ([MODELS_PATH,CONTROLLERS_PATH] as $dir) {
        $path = $dir . '/' . $class . '.php';
        if (is_file($path)) { require_once $path; return; }
    }
});
function e($value): string { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $route='home'): string { return BASE_URL . '/index.php?route=' . rawurlencode($route); }
function asset(string $path): string { return BASE_URL . '/views/assets/' . $path; }
function css(string $name): string { return asset('css/' . $name . '.css'); }
function js(string $name): string { return asset('js/' . $name . '.js'); }
function img(string $name): string { return asset('img/' . $name); }
function redirect(string $route): void { header('Location: '.url($route), true, 303); exit; }
function render_view(string $viewName, array $data=[], ?string $layout='header'): void {
    if (!preg_match('/\A[a-z0-9_]+\z/D',$viewName) || !is_file(VIEWS_PATH.'/'.$viewName.'.php')) {
        http_response_code(404); $viewName='404';
    }
    extract($data, EXTR_SKIP);
    if ($layout) require INCLUDES_PATH . '/header.php';
    require VIEWS_PATH . '/' . $viewName . '.php';
    if ($layout) require INCLUDES_PATH . '/footer.php';
}
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): void { echo '<input type="hidden" name="csrf" value="'.e(csrf()).'">'; }
function post_guard(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); render_view('error',['title'=>'Método não permitido','message'=>'Use o formulário para continuar.']); exit; }
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals(csrf(),$_POST['csrf'])) {
        header('HTTP/1.1 419 Form Expired', true, 419); render_view('error',['title'=>'Formulário expirado','message'=>'Atualize a página e tente novamente.']); exit;
    }
}
function input(string $key): string { return is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : ''; }
function secret_input(string $key): string { return is_string($_POST[$key] ?? null) ? $_POST[$key] : ''; }
function password_ok(string $password): bool { return preg_match_all('/./us',$password) >= 10 && strlen($password) <= 72; }
function flash(string $message, string $kind='success'): void { $_SESSION['flash']=['message'=>$message,'kind'=>$kind]; }
function current_user(): ?array {
    $id = (int)($_SESSION['user_id'] ?? 0);
    if (!$id) return null;
    if (time() - (int)($_SESSION['last_active'] ?? 0) > 3600) { unset($_SESSION['user_id']); return null; }
    $_SESSION['last_active']=time();
    $user=(new User())->find($id);
    if (!$user || !hash_equals($_SESSION['auth_signature'] ?? '', hash('sha256',$user['password']))) { unset($_SESSION['user_id'],$_SESSION['auth_signature']);return null; }
    unset($user['password']);
    return $user;
}
function require_user(bool $admin=false): array {
    $user=current_user();
    if (!$user) { flash('Entre na sua conta para continuar.','info'); redirect('auth_login'); }
    if ($admin && $user['user_type'] !== 'adm') { http_response_code(403); render_view('error',['title'=>'Acesso restrito','message'=>'Sua conta não possui permissão para esta página.']); exit; }
    return $user;
}
function throttle(string $action, int $limit, int $window=900): void {
    // Arquivo compartilhado por IP/ação: não pode ser reiniciado apagando cookies.
    $key=hash('sha256',$action.'|'.($_SERVER['REMOTE_ADDR'] ?? 'cli'));
    $file=BASE_PATH.'/log/rate-'.$key.'.json';
    $fp=fopen($file,'c+');
    if (!$fp || !flock($fp,LOCK_EX)) throw new RuntimeException('Não foi possível aplicar o limite de requisições.');
    @chmod($file,0600);
    $events=json_decode(stream_get_contents($fp) ?: '[]',true) ?: [];
    $events=array_values(array_filter($events,fn($time)=>$time > time()-$window));
    if (count($events)>=$limit) { flock($fp,LOCK_UN);fclose($fp);http_response_code(429);header('Retry-After: '.$window);render_view('error',['title'=>'Um pequeno intervalo','message'=>'Foram feitas muitas tentativas. Aguarde alguns minutos e tente novamente.']);exit; }
    $events[]=time(); rewind($fp);ftruncate($fp,0);fwrite($fp,json_encode($events));flock($fp,LOCK_UN);fclose($fp);
}
function log_failure(Throwable $ex): void {
    // Não registrar valores SQL, senhas, tokens, cookies nem mensagens que possam contê-los.
    error_log(date('c').' '.get_class($ex).' code='.$ex->getCode().' at '.basename($ex->getFile()).':'.$ex->getLine().PHP_EOL,3,BASE_PATH.'/log/erros.log');
    @chmod(BASE_PATH.'/log/erros.log',0600);
}
