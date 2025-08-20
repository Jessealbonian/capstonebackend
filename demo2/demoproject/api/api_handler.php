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
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Set JSON content type
header("Content-Type: application/json");

// Get the endpoint from the URL
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Extract the endpoint name (remove /api/ prefix)
$endpoint = '';
if (strpos($path, '/api/') === 0) {
    $endpoint = substr($path, 5); // Remove '/api/'
} elseif (strpos($path, '/api') === 0) {
    $endpoint = substr($path, 4); // Remove '/api'
}

// Remove any trailing slashes
$endpoint = trim($endpoint, '/');

// Include required modules
require_once "./modules/get.php";
require_once "./modules/post.php";
require_once "./config/database.php";

try {
    $con = new Connection();
    $pdo = $con->connect();
    $get = new Get($pdo);
    $post = new Post($pdo);
    
    // Route the request based on the endpoint
    switch ($endpoint) {
        case 'addClass':
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($post->addClass($data));
            break;
            
        case 'generateTokens':
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($post->generateTokens($data));
            break;
            
        case 'getClasses':
            echo json_encode($get->getClasses());
            break;
            
        case 'deleteClass':
            $data = json_decode(file_get_contents("php://input"), true);
            // Note: deleteClass method doesn't exist, you may need to implement it
            http_response_code(501);
            echo json_encode(['error' => 'deleteClass method not implemented']);
            break;
            
        case 'editClass':
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($post->editClass($data));
            break;
            
        case 'login_users':
            $data = json_decode(file_get_contents("php://input"), true);
            echo $post->login_users($data);
            break;
            
        case 'signup_users':
            $data = json_decode(file_get_contents("php://input"), true);
            echo $post->signup_users($data);
            break;
            
        case 'admin_login':
            $data = json_decode(file_get_contents("php://input"), true);
            echo $post->adminLogin($data);
            break;
            
        case 'signup':
            $data = json_decode(file_get_contents("php://input"), true);
            echo $post->signup($data);
            break;
            
        case 'Hoa_adminsignup':
            $data = json_decode(file_get_contents("php://input"), true);
            echo $post->HOA_adminSignup($data);
            break;
            
        case 'Hoa_adminlogin':
            $data = json_decode(file_get_contents("php://input"), true);
            echo $post->HOA_adminLogin($data);
            break;
            
        case 'getImage':
            echo json_encode($get->getImage());
            break;
            
        default:
            // Check if it's an enrolled-classes request with ID
            if (strpos($endpoint, 'enrolled-classes/id/') === 0) {
                $parts = explode('/', $endpoint);
                if (count($parts) >= 3) {
                    $userId = intval($parts[2]);
                    echo json_encode($get->getEnrolledClassesById($userId));
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid enrolled-classes request']);
                }
            }
            // Check if it's a user-class-routines request with ID
            elseif (strpos($endpoint, 'user-class-routines/id/') === 0) {
                $parts = explode('/', $endpoint);
                if (count($parts) >= 3) {
                    $userId = intval($parts[2]);
                    echo json_encode($get->getUserClassRoutinesById($userId));
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid user-class-routines request']);
                }
            }
            // Check if it's a class-routines request
            elseif (strpos($endpoint, 'class-routines/') === 0) {
                $parts = explode('/', $endpoint);
                if (count($parts) >= 2) {
                    $classId = intval($parts[1]);
                    echo json_encode($get->getClassRoutines($classId));
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid class-routines request']);
                }
            }
            // Check if it's a class-info request
            elseif (strpos($endpoint, 'class-info/') === 0) {
                $parts = explode('/', $endpoint);
                if (count($parts) >= 2) {
                    $classId = intval($parts[1]);
                    echo json_encode($get->getClassInfoById($classId));
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid class-info request']);
                }
            }
            // Check if it's a routine-history request
            elseif (strpos($endpoint, 'routine-history/') === 0) {
                $parts = explode('/', $endpoint);
                if (count($parts) >= 2) {
                    $studentUsername = $parts[1];
                    echo json_encode($get->getRoutineHistory($studentUsername));
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid routine-history request']);
                }
            }
            // Check if it's a check-today request
            elseif (strpos($endpoint, 'check-today/') === 0) {
                $parts = explode('/', $endpoint);
                if (count($parts) >= 3) {
                    $routineId = intval($parts[1]);
                    $studentUsername = $parts[2];
                    echo json_encode($get->checkTodayRoutine($routineId, $studentUsername));
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid check-today request']);
                }
            }
            else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint not found: ' . $endpoint]);
            }
            break;
    }
    
} catch (Exception $e) {
    error_log("Error in api_handler: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "API error: " . $e->getMessage()]);
}
?>
