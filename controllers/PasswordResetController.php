<?php
class PasswordResetController {
 public function forgot(): void { render_view('forgot_password',['title'=>'Recupere o acesso']); }
 public function request(): void {
  post_guard();throttle('reset',4);$email=strtolower(input('email'));
  if(filter_var($email,FILTER_VALIDATE_EMAIL) && strlen($email)<=150){
   $user=(new User())->byEmail($email);
   if($user){$token=bin2hex(random_bytes(32));(new User())->setReset((int)$user['id'],hash('sha256',$token),gmdate('Y-m-d H:i:s',time()+1800));
    $base=getenv('APP_BASE_URL');if(!$base || !preg_match('#\Ahttps?://#',$base))throw new RuntimeException('APP_BASE_URL precisa ser configurada.');
    $link=url('password_reset').'&token='.$token;
    if(getenv('MAIL_MODE')==='mail'){
     $from=getenv('MAIL_FROM');if(!filter_var($from,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Remetente inválido.');
     if(!mail($email,'Recuperar acesso - Mundo Invertido',"Defina uma nova senha em até 30 minutos:\n".$link,"From: ".$from))throw new RuntimeException('Envio de email indisponível.');
    } else { $path=BASE_PATH.'/log/reset-mail.log';file_put_contents($path,date('c').' link local: '.$link.PHP_EOL,FILE_APPEND|LOCK_EX);chmod($path,0600); }
    (new Audit())->record((int)$user['id'],'reset_requested','Recuperação solicitada.');
   }
  }
  flash('Se o e-mail estiver cadastrado, você receberá as instruções de recuperação.');redirect('password_forgot');
 }
 public function reset(): void { $token=is_string($_GET['token']??null)?$_GET['token']:'';render_view('reset_password',['title'=>'Nova senha','token'=>$token]); }
 public function update(): void {
  post_guard();throttle('reset-update',8);$token=input('token');$password=secret_input('password');$errors=[];
  if(!preg_match('/\A[a-f0-9]{64}\z/',$token))$errors[]='O link é inválido. Solicite outro.';
  if(!password_ok($password) || !hash_equals($password,secret_input('password_confirm')))$errors[]='Use pelo menos 10 caracteres, evite uma senha muito longa e confirme com o mesmo valor.';
  if(!$errors && !(new User())->reset($token,$password))$errors[]='O link expirou ou já foi usado. Solicite outro.';
  if($errors){http_response_code(422);render_view('reset_password',['title'=>'Nova senha','errors'=>$errors,'token'=>$token]);return;}
  $_SESSION=[];session_regenerate_id(true);flash('Senha atualizada. Entre novamente.');redirect('auth_login');
 }
}
