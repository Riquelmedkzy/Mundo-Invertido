<?php
class ProfileController {
 public function index(): void { $user=require_user();render_view('profile',['title'=>'Seu perfil','profile'=>$user]); }
 public function save(): void {
  post_guard();$user=require_user();throttle('profile',20);$name=input('name');$phone=input('phone');$errors=[];$bytes=null;
  if(strlen($name)<2 || strlen($name)>100 || !preg_match('/\A[0-9+() .-]{8,20}\z/',$phone))$errors[]='Revise seu nome e telefone.';
  $photo=$_FILES['photo'] ?? null;
  if($photo && $photo['error']!==UPLOAD_ERR_NO_FILE){
   if($photo['error']!==UPLOAD_ERR_OK || $photo['size']>2*1024*1024 || !is_uploaded_file($photo['tmp_name']))$errors[]='A foto deve ter no máximo 2 MB.';
   else { $mime=(new finfo(FILEINFO_MIME_TYPE))->file($photo['tmp_name']);$info=@getimagesize($photo['tmp_name']);if(!in_array($mime,['image/jpeg','image/png','image/webp'],true) || !$info || $info[0]>4096 || $info[1]>4096)$errors[]='Use uma imagem JPG, PNG ou WebP de até 4096 × 4096 pixels.';else $bytes=file_get_contents($photo['tmp_name']); }
  }
  if($errors){http_response_code(422);render_view('profile',['title'=>'Seu perfil','profile'=>array_merge($user,compact('name','phone')),'errors'=>$errors]);return;}
  $db=Database::connection();$db->begin_transaction();
  try { $model=new User();$model->update((int)$user['id'],$name,$phone);if($bytes!==null)$model->savePhoto((int)$user['id'],$bytes);(new Audit())->record((int)$user['id'],'profile_updated','Perfil atualizado.');$db->commit(); } catch(Throwable $e){$db->rollback();throw $e;}
  flash('Perfil atualizado.');redirect('profile');
 }
 public function photo(): void {
  $user=require_user();$bytes=(new User())->photo((int)$user['id']);
  header('Cache-Control: private, no-store');
  if(!$bytes){header('Content-Type: image/svg+xml');echo '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160"><rect width="160" height="160" fill="#291719"/><circle cx="80" cy="59" r="26" fill="#c0a9a5"/><path d="M24 145a56 56 0 0 1 112 0" fill="#c0a9a5"/></svg>';return;}
  header('Content-Type: '.(new finfo(FILEINFO_MIME_TYPE))->buffer($bytes));echo $bytes;
 }
}
