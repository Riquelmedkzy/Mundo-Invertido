<?php
class Database {
 private static ?mysqli $connection=null;
 public static function connection(): mysqli {
  if (self::$connection) return self::$connection;
  try {
   mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
   $db=new mysqli(getenv('DB_HOST') ?: '127.0.0.1',getenv('DB_USER') ?: 'root',getenv('DB_PASSWORD') ?: '',getenv('DB_NAME') ?: 'projinvertido_db',(int)(getenv('DB_PORT') ?: 3306));
   $db->set_charset('utf8mb4'); self::$connection=$db;return $db;
  } catch (Throwable $ex) { log_failure($ex); throw new RuntimeException('Banco indisponível.'); }
 }
 public static function run(string $sql, array $params=[]): mysqli_stmt {
  $stmt=self::connection()->prepare($sql);
  if ($params) { $types='';foreach($params as $v) $types.=is_int($v)?'i':'s';$stmt->bind_param($types,...$params); }
  $stmt->execute();return $stmt;
 }
}
