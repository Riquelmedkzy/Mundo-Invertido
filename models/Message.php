<?php
class Message {
 const STATUSES=['Pendente','Lida','Respondida','Resolvida','Em Análise','Lixeira'];
 public function create(?int $user,string $name,string $email,string $subject,string $message): void { Database::run('INSERT INTO messages (user_id,name,email,subject,message) VALUES (?,?,?,?,?)',[$user,$name,$email,$subject,$message]); }
 public function page(int $offset): array { return Database::run('SELECT * FROM messages ORDER BY id DESC LIMIT 20 OFFSET ?',[$offset])->get_result()->fetch_all(MYSQLI_ASSOC); }
 public function status(int $id,string $status,int $actor): void {
  $db=Database::connection();$db->begin_transaction();
  try { Database::run('UPDATE messages SET status=? WHERE id=?',[$status,$id]);(new Audit())->record($actor,'message_status','Mensagem #'.$id.': '.$status);$db->commit(); } catch(Throwable $e){$db->rollback();throw $e;}
 }
}
