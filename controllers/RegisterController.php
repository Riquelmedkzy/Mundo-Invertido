<?php
class RegisterController {
 public function index(): void { render_view('register',['title'=>'Entre para o clube']); }
 public function add(): void {
  post_guard();throttle('register',8);
  $name=input('name');$email=strtolower(input('email'));$phone=input('phone');$password=secret_input('password');$errors=[];
  if (strlen($name)<2 || strlen($name)>100) $errors[]='Informe um nome entre 2 e 100 caracteres.';
  if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>150) $errors[]='Informe um e-mail válido.';
  if (!preg_match('/\A[0-9+() .-]{8,20}\z/',$phone)) $errors[]='Informe um telefone válido, com DDD.';
  if (!password_ok($password)) $errors[]='Use pelo menos 10 caracteres na senha. Se ela for muito longa, reduza o tamanho.';
  if (!hash_equals($password,secret_input('password_confirm'))) $errors[]='As senhas precisam ser iguais.';
  if ($errors) { http_response_code(422);render_view('register',['title'=>'Revise seu cadastro','errors'=>$errors,'old'=>['name'=>$name,'email'=>$email,'phone'=>$phone]]);return; }
  $db=Database::connection();$db->begin_transaction();
  try { $id=(new User())->create($name,$email,$password,$phone);(new Audit())->record($id,'registered','Conta criada.');$db->commit(); }
  catch(mysqli_sql_exception $e) { $db->rollback();if ((int)$e->getCode()!==1062) throw $e;http_response_code(422);render_view('register',['title'=>'Revise seu cadastro','errors'=>['Não foi possível cadastrar esse e-mail. Tente entrar ou recuperar sua senha.'],'old'=>['name'=>$name,'email'=>$email,'phone'=>$phone]]);return; }
  catch(Throwable $e) { $db->rollback();throw $e; }
  flash('Seu lugar no clube está garantido. Entre na sua conta.');redirect('auth_login');
 }
}
