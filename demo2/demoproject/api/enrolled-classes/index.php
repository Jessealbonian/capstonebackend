<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set CORS headers immediately
$allowed_origins = [
    "https://athletrack.vercel.app",
    "https://athletrack-git-main-kohitrees-projects.vercel.app",
    "https://athletrack-kicwhvrgv-kohitrees-projects.vercel.app",
    "http://localhost:4200",
    "https://capstonebackend-9wrj.onrender.com"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? "";

// Set CORS headers
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Set JSON content type
header("Content-Type: application/json");

// Include required modules
require_once "../modules/get.php";
require_once "../config/database.php";

try {
    $con = new Connection();
    $pdo = $con->connect();
    $get = new Get($pdo);
    
    // Get the user ID from the URL path
    $request_uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($request_uri, PHP_URL_PATH);
    $path_parts = explode('/', trim($path, '/'));
    
    // Look for the pattern /api/enrolled-classes/id/{userId}
    $userId = null;
    for ($i = 0; $i < count($path_parts) - 1; $i++) {
        if ($path_parts[$i] === 'enrolled-classes' && 
            isset($path_parts[$i + 1]) && $path_parts[$i + 1] === 'id' && 
            isset($path_parts[$i + 2])) {
            $userId = intval($path_parts[$i + 2]);
            break;
        }
    }
    
    if ($userId) {
        echo json_encode($get->getEnrolledClassesById($userId));
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'user_id is required']);
    }
    
} catch (Exception $e) {
    error_log("Error in enrolled-classes: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Failed to get enrolled classes: " . $e->getMessage()]);
}
?>
