<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$allowed_origins = [
    "https://athletrack.vercel.app",
    "https://athletrack-git-main-kohitrees-projects.vercel.app",
    "https://athletrack-kicwhvrgv-kohitrees-projects.vercel.app",
    "http://localhost:4200",
    "https://capstonebackend-9wrj.onrender.com"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? "";
error_log("CORS Debug - Requested Origin: " . $origin);
error_log("CORS Debug - Allowed Origins: " . implode(", ", $allowed_origins));

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    error_log("CORS Debug - Set Access-Control-Allow-Origin: $origin");
} else {
    error_log("CORS Debug - Origin not in allowed list: $origin");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

error_log("CORS Debug - Request Method: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    error_log("CORS Debug - Handling OPTIONS preflight request");
    http_response_code(200);
    exit;
}
?>