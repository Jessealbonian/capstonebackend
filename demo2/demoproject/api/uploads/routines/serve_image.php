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

// Get the image filename from the URL
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// Look for the image filename in the path
$image_filename = null;
for ($i = 0; $i < count($path_parts); $i++) {
    if (isset($path_parts[$i]) && 
        (strpos($path_parts[$i], '.PNG') !== false || 
         strpos($path_parts[$i], '.png') !== false ||
         strpos($path_parts[$i], '.jpg') !== false ||
         strpos($path_parts[$i], '.jpeg') !== false)) {
        $image_filename = $path_parts[$i];
        break;
    }
}

if ($image_filename) {
    $image_path = __DIR__ . '/' . $image_filename;
    
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
            default:
                header('Content-Type: application/octet-stream');
        }
        
        // Output the image
        readfile($image_path);
    } else {
        http_response_code(404);
        echo "Image not found";
    }
} else {
    http_response_code(400);
    echo "No image filename provided";
}
?>
