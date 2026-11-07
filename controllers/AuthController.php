<?php
class AuthController {
 public function login(): void { render_view('login',['title'=>'Área restrita']); }
 public function authenticate(): void {
  post_guard();throttle('login',10);
  $email=strtolower(input('email'));$password=secret_input('password');
  $user=strlen($email)<=150 ? (new User())->byEmail($email) : null;
  // Hash fixo sintético equaliza o trabalho quando a conta não existe.
  $valid=password_verify($password,$user['password'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
  if (!$user || !$valid) { if($user)(new Audit())->record((int)$user['id'],'login_failed','Tentativa de acesso recusada.');http_response_code(422);render_view('login',['title'=>'Área restrita','errors'=>['E-mail ou senha incorretos.'],'old'=>['email'=>$email]]);return; }
  if(password_needs_rehash($user['password'],PASSWORD_DEFAULT))(new User())->rehash((int)$user['id'],$password);
  session_regenerate_id(true);$_SESSION['user_id']=(int)$user['id'];$_SESSION['last_active']=time();$_SESSION['auth_signature']=hash('sha256',(new User())->byEmail($email)['password']);unset($_SESSION['csrf']);
  (new Audit())->record((int)$user['id'],'login','Acesso realizado.');redirect($user['user_type']==='adm'?'admin':'profile');
 }
 public function logout(): void { post_guard();$u=require_user();(new Audit())->record((int)$u['id'],'logout','Sessão encerrada.');$_SESSION=[];session_regenerate_id(true);flash('Você saiu da sua conta.');redirect('home'); }
}
