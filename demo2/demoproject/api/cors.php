<?php
$allowed_origins = [
    "https://athletrack.vercel.app",
    "https://athletrack-git-main-kohitrees-projects.vercel.app",
    "https://athletrack-kicwhvrgv-kohitrees-projects.vercel.app",
    "http://localhost:4200",
    "https://capstonebackend-9wrj.onrender.com"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? "";
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // if you use cookies/session

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
