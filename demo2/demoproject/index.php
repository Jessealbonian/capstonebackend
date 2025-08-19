<?php
// CORS (adjust origins below)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
$allowed = ['*']; // replace with your front-end origin(s) later
if (in_array('*', $allowed) || in_array($origin, $allowed)) {
  header("Access-Control-Allow-Origin: " . (in_array('*', $allowed) ? '*' : $origin));
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
  header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS");
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/' || $path === '/health') {
  header('Content-Type: application/json');
  echo json_encode(['status' => 'ok']);
  exit;
}

if ($path === '/db-test') {
  require __DIR__ . '/src/db.php';
  header('Content-Type: application/json');
  try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(", ", $tables);
    
  } catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['db' => 'error', 'message' => $e->getMessage()]);
  }
  exit;
}

echo "Backend is running 🚀";


http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not found']);