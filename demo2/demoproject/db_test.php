<?php
$host = getenv('MYSQLHOST') ?: 'crossover.proxy.rlwy.net';
$port = getenv('MYSQLPORT') ?: 3306;
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD');

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connected to database successfully!<br>";

    // Optional: test query
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(", ", $tables);

} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
