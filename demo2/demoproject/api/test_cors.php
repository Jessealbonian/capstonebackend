<?php
// Simple CORS test without database
include_once __DIR__ . "/cors.php";

echo json_encode([
    "status" => "success",
    "message" => "CORS test working",
    "origin" => $_SERVER['HTTP_ORIGIN'] ?? 'none',
    "method" => $_SERVER['REQUEST_METHOD']
]);
?>