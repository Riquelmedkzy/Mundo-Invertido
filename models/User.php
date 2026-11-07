<?php
class User {
 public function find(int $id): ?array { return Database::run('SELECT id,name,email,phone,user_type,created_at,password FROM users WHERE id=?',[$id])->get_result()->fetch_assoc(); }
 public function byEmail(string $email): ?array { return Database::run('SELECT * FROM users WHERE email=?',[$email])->get_result()->fetch_assoc(); }
 public function create(string $name,string $email,string $password,string $phone): int {
  Database::run('INSERT INTO users (name,email,password,phone) VALUES (?,?,?,?)',[$name,$email,password_hash($password,PASSWORD_DEFAULT),$phone]);return Database::connection()->insert_id;
 }
 public function page(int $offset): array { return Database::run('SELECT id,name,email,user_type,created_at FROM users ORDER BY id DESC LIMIT 20 OFFSET ?',[$offset])->get_result()->fetch_all(MYSQLI_ASSOC); }
 public function update(int $id,string $name,string $phone): void { Database::run('UPDATE users SET name=?,phone=? WHERE id=?',[$name,$phone,$id]); }
 public function photo(int $id): ?string { $r=Database::run('SELECT foto_perfil FROM perfil WHERE user_id=?',[$id])->get_result()->fetch_assoc();return $r['foto_perfil'] ?? null; }
 public function savePhoto(int $id,string $bytes): void { Database::run('INSERT INTO perfil (user_id,foto_perfil) VALUES (?,?) ON DUPLICATE KEY UPDATE foto_perfil=?',[$id,$bytes,$bytes]); }
 public function rehash(int $id,string $password): void { Database::run('UPDATE users SET password=? WHERE id=?',[password_hash($password,PASSWORD_DEFAULT),$id]); }
 public function setReset(int $id,string $hash,string $expires): void { Database::run('UPDATE users SET reset_token=?,reset_expires=? WHERE id=?',[$hash,$expires,$id]); }
 public function reset(string $token,string $password): bool {
  $db=Database::connection();$db->begin_transaction();
  try {
   $u=Database::run('SELECT id FROM users WHERE reset_token=? AND reset_expires>UTC_TIMESTAMP() FOR UPDATE',[hash('sha256',$token)])->get_result()->fetch_assoc();
   if (!$u) { $db->rollback();return false; }
   Database::run('UPDATE users SET password=?,reset_token=NULL,reset_expires=NULL WHERE id=?',[password_hash($password,PASSWORD_DEFAULT),(int)$u['id']]);
   (new Audit())->record((int)$u['id'],'password_reset','Senha redefinida.');$db->commit();return true;
  } catch(Throwable $e) { $db->rollback();throw $e; }
 }
 public function changeRole(int $actor,int $target,string $role): bool {
  $db=Database::connection();$db->begin_transaction();
  try {
   $admins=Database::run("SELECT id FROM users WHERE user_type='adm' ORDER BY id FOR UPDATE")->get_result()->fetch_all(MYSQLI_ASSOC);
   $user=Database::run('SELECT user_type FROM users WHERE id=? FOR UPDATE',[$target])->get_result()->fetch_assoc();
   if (!$user || ($user['user_type']==='adm' && $role==='usu' && count($admins)<=1)) { $db->rollback();return false; }
   Database::run('UPDATE users SET user_type=? WHERE id=?',[$role,$target]);
   (new Audit())->record($actor,'permission_changed','Usuário #'.$target.' definido como '.$role);$db->commit();return true;
  } catch(Throwable $e) { $db->rollback();throw $e; }
 }
}
