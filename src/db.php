<?php
function get_db(): PDO {
  $host = getenv('MYSQLHOST') ?: '127.0.0.1';
  $port = getenv('MYSQLPORT') ?: '3306';
  $db   = getenv('MYSQLDATABASE') ?: 'railway';
  $user = getenv('MYSQLUSER') ?: 'root';
  $pass = getenv('MYSQLPASSWORD') ?: '';
  $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}
 