<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start output buffering
ob_start();

// Include CORS
include_once __DIR__ . "/cors.php";

// Set JSON content type
header("Content-Type: application/json");

// Simple test response for now
if (isset($_REQUEST['request'])) {
    $request = explode('/', $_REQUEST['request']);
    
    if ($request[0] === 'login_users') {
        echo json_encode([
            "status" => "success",
            "message" => "login_users endpoint reached",
            "method" => $_SERVER['REQUEST_METHOD']
        ]);
                } else {
        echo json_encode([
            "status" => "error",
            "message" => "Unknown endpoint: " . $request[0]
        ]);
                    }
                } else {
    echo json_encode([
        "status" => "error",
        "message" => "No request parameter"
    ]);
}

// Flush output buffer
ob_end_flush();
?>
