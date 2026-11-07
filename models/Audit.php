<?php
class Audit {
 public function record(int $user,string $action,string $details=''): void { Database::run('INSERT INTO audit_log (user_id,action_type,details) VALUES (?,?,?)',[$user,$action,$details]); }
 public function page(int $offset): array { return Database::run('SELECT a.*,u.name FROM audit_log a JOIN users u ON u.id=a.user_id ORDER BY a.log_id DESC LIMIT 20 OFFSET ?',[$offset])->get_result()->fetch_all(MYSQLI_ASSOC); }
}
