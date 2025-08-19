<?php
// ===== CONFIG =====
$allowed_origins = [
    "https://athletrack.vercel.app",
    "https://athletrack-git-main-kohitrees-projects.vercel.app",
    "https://athletrack-kicwhvrgv-kohitrees-projects.vercel.app",
    "http://localhost:4200",
    "https://capstonebackend-9wrj.onrender.com"
];

// Database credentials from Railway (adjust accordingly)
$db_host = "crossover.proxy.rlwy.net"; 
$db_user = "root";
$db_pass = "";
$db_name = "railway";

// ===== CORS HANDLER =====
$origin = $_SERVER['HTTP_ORIGIN'] ?? "";
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ===== PATH CHECKER =====
$request_uri = $_SERVER['REQUEST_URI'] ?? "";
echo "Hello, This is my api testing.<br>";
echo "Requested Path: " . htmlspecialchars($request_uri) . "<br><br>";

// ===== DB CHECKER =====
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "DB Connection failed: " . $mysqli->connect_error;
} else {
    echo "DB Connected Successfully.<br>";
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        echo "Tables in DB:<br>";
        while ($row = $result->fetch_array()) {
            echo "- " . htmlspecialchars($row[0]) . "<br>";
        }
    } else {
        echo "Error fetching tables: " . $mysqli->error;
    }
    $mysqli->close();
}

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
