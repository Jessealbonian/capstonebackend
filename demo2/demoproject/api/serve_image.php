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
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the image path from the URL
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Extract the path after /api/serve_image/
$api_path = '/api/serve_image/';
if (strpos($path, $api_path) === 0) {
    $image_relative_path = substr($path, strlen($api_path));
    
    // Build the full path to the image
    $image_path = __DIR__ . '/uploads/' . $image_relative_path;
    
    if (file_exists($image_path)) {
        // Get file info
        $file_info = pathinfo($image_path);
        $extension = strtolower($file_info['extension']);
        
        // Set appropriate content type
        switch ($extension) {
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'gif':
                header('Content-Type: image/gif');
                break;
            default:
                header('Content-Type: application/octet-stream');
        }
        
        // Output the image
        readfile($image_path);
    } else {
        http_response_code(404);
        echo "Image not found: " . $image_relative_path;
    }
} else {
    http_response_code(400);
    echo "Invalid image path";
}
?>
