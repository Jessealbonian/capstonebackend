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
require_once "./modules/post.php";
require_once "./config/database.php";

try {
    $con = new Connection();
    $pdo = $con->connect();
    $post = new Post($pdo);
    
    // Get the request data
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Call the generateTokens function
    echo json_encode($post->generateTokens($data));
    
} catch (Exception $e) {
    error_log("Error in apigenerateTokens: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Failed to generate tokens: " . $e->getMessage()]);
}
?>
