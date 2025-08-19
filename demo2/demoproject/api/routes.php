<?php
// Enable error reporting at the very top
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start output buffering to catch any early errors
ob_start();

include_once __DIR__ . "/cors.php";

// Add debugging to see if CORS headers are being set
error_log("=== REQUEST START ===");
error_log("CORS Headers - Origin: " . ($_SERVER['HTTP_ORIGIN'] ?? 'none'));
error_log("CORS Headers - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("CORS Headers - Request URI: " . $_SERVER['REQUEST_URI']);

// Set JSON content type
header("Content-Type: application/json");

// Handle OPTIONS request for preflight check (this is necessary for some browsers to allow cross-origin requests)
//if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Respond with the necessary CORS headers for preflight checks
    //header("Access-Control-Allow-Origin: *");
    //header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
    //header("Access-Control-Allow-Headers: Content-Type, Authorization");
    // exit(0); // Exit after handling the OPTIONS request
//}


// Include required modules with error handling
try {
    require_once "./modules/get.php";
    error_log("get.php loaded successfully");
} catch (Exception $e) {
    error_log("Error loading get.php: " . $e->getMessage());
}

try {
    require_once "./modules/post.php";
    error_log("post.php loaded successfully");
} catch (Exception $e) {
    error_log("Error loading post.php: " . $e->getMessage());
}

try {
    require_once "./config/database.php";
    error_log("database.php loaded successfully");
} catch (Exception $e) {
    error_log("Error loading database.php: " . $e->getMessage());
}

// Add error handling for database connection
try {
    $con = new Connection();
    $pdo = $con->connect();
    error_log("Database connection successful");
    
    // Test if the hoa_users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'hoa_users'");
    if ($stmt->rowCount() > 0) {
        error_log("hoa_users table exists");
    } else {
        error_log("hoa_users table does not exist");
    }
    
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Initialize Get and Post objects
try {
    $get = new Get($pdo);
    $post = new Post($pdo);
    error_log("Get and Post objects initialized successfully");
} catch (Exception $e) {
    error_log("Error initializing Get/Post objects: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Service initialization failed"]);
    exit;
}

// Check if 'request' parameter is set in the request
if (isset($_REQUEST['request'])) {
    $request = explode('/', $_REQUEST['request']);
    error_log("Request parameter: " . $_REQUEST['request']);
} else {
    error_log("No request parameter found");
    echo "Not Found";
    http_response_code(404);
    exit;
}

// Handle requests based on HTTP method
switch ($_SERVER['REQUEST_METHOD']) {
        // Handle GET requests
    case 'GET':
        switch ($request[0]) {
            case 'getTasks':
                echo json_encode($get->get_tasks());
                break;
                // case 'todolist':
                //     // Return JSON-encoded data for getting employees
                //     if (count($request) > 1) {
                //         echo json_encode($get->getstatus($request[1]));
                //     } else {
                //         echo json_encode($get->getstatus($data));
                //     }
                //     break;



                // case '1todolist':
                //     // Return JSON-encoded data for getting jobs
                //     if (count($request) > 1) {
                //         echo json_encode($get->gettask($request[1]));
                //     } else {
                //         echo json_encode($get->gettask($data));
                //     }
                //     break;


                // case 'todoliststatus':
                //     // Return JSON-encoded data for getting jobs
                //     if (count($request) > 1) {
                //         echo json_encode($get->gettask_status($request[1]));
                //     } else {
                //         echo json_encode($get->gettask_status($data));
                //     }
                //     break;

                // HOME FINDER

            case 'get_properties':
                echo json_encode($get->get_properties());
                break;

            case 'get_admins':
                if (count($request) > 1) {
                    echo json_encode($get->get_adminss($request[1]));
                } else {
                    echo json_encode($get->get_adminss($data));
                }
                break;

            case 'getMedia':
                echo json_encode($get->getMedia());
                break;

            case 'get_media':
                if (count($request) > 1) {
                    echo json_encode($get->get_Media($request[1]));
                } else {
                    echo json_encode($get->get_Media('news')); // Default to news if no type is provided
                }
                break;

            case 'get_request':
                if (count($request) > 1) {
                    echo json_encode($get->get_Requests($request[1]));
                } else {
                    echo json_encode($get->get_Requests('inquiries')); // Default to news if no type is provided
                }
                break;

            case 'getInquiries':
                echo json_encode($get->getInquiries());
                break;

            case 'getTrippingRequests':
                echo json_encode($get->getTrippingRequests());
                break;

            case 'getCustomerCareRequests':
                echo json_encode($get->getCustomerCareRequests());
                break;

            case 'getUnitTurnOvers':
                echo json_encode($get->getUnitTurnOvers());
                break;

            case 'getProf':
                echo json_encode($get->getprof($request[1]));
                break;

            case 'getUserProf':
                echo json_encode($get->getUserProf($request[1]));
                break;

            case 'getHoaUserProf':
                echo json_encode($get->getHoaUserProf($request[1]));
                break;

            case 'getHoaAdminProf':
                echo json_encode($get->getHoaAdminProf($request[1]));
                break;

            case 'getClasses':
                echo json_encode($get->getClasses());
                break;

            case 'getClassAttendance':
                // expects class_id, year, month, day as query params
                $classId = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;
                $year = isset($_GET['year']) ? intval($_GET['year']) : null;
                $month = isset($_GET['month']) ? intval($_GET['month']) : null;
                $day = isset($_GET['day']) ? intval($_GET['day']) : null;
                echo json_encode($get->getClassAttendance($classId, $year, $month, $day));
                break;

            // Routines endpoints
            case 'enrolled-classes':
                if (count($request) > 2 && $request[1] === 'id') {
                    // GET /enrolled-classes/id/{userId}
                    $userId = intval($request[2]);
                    echo json_encode($get->getEnrolledClassesById($userId));
                } else if (count($request) > 1) {
                    // Backward-compatibility: username segment but requires ?user_id= query
                    $studentUsername = $request[1];
                    echo json_encode($get->getEnrolledClasses($studentUsername));
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'user_id is required']);
                }
                break;

            case 'class-routines':
                if (count($request) > 1) {
                    $classId = intval($request[1]);
                    echo json_encode($get->getClassRoutines($classId));
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Class ID is required']);
                }
                break;

            case 'class-info':
                if (count($request) > 1) {
                    $classId = intval($request[1]);
                    echo json_encode($get->getClassInfoById($classId));
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Class ID is required']);
                }
                break;

            // Fetch all routines for classes a user has redeemed in codegen
            case 'user-class-routines':
                if (count($request) > 2 && $request[1] === 'id') {
                    // GET /user-class-routines/id/{userId}
                    $userId = intval($request[2]);
                    echo json_encode($get->getUserClassRoutinesById($userId));
                } else if (count($request) > 1) {
                    // Backward-compatibility: username with ?user_id= query
                    $studentUsername = $request[1];
                    echo json_encode($get->getUserClassRoutines($studentUsername));
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'user_id is required']);
                }
                break;

            case 'routine-history':
                if (count($request) > 1) {
                    $studentUsername = $request[1];
                    echo json_encode($get->getRoutineHistory($studentUsername));
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Student username is required']);
                }
                break;

            case 'check-today':
                if (count($request) > 2) {
                    $routineId = intval($request[1]);
                    $studentUsername = $request[2];
                    echo json_encode($get->checkTodayRoutine($routineId, $studentUsername));
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Routine ID and student username are required']);
                }
                break;

            case 'getimages':
                echo json_encode($get->getImage());
                break;

            case 'getApplications':
                echo json_encode($get->getApplications());
                break;

            case 'getApprovedApplications':
                if (count($request) > 1) {
                    echo json_encode($get->getApprovedApplications($request[1]));
                } else {
                    echo json_encode($get->getApprovedApplications());
                }
                break;

            case 'get_news':
                echo $get->get_news();
                break;

            case 'get_blogs':
                echo $get->get_blogs();
                break;

            case 'get_vlogs':
                echo $get->get_vlogs();   //MUST --> need to fetch the VIDEO from URL link
                break;

            case 'getPersonalApplication':
                if (count($request) > 1) {
                    echo json_encode($get->getPersonalApplication($request[1]));
                } else {
                    echo json_encode($get->getPersonalApplication($data));
                }
                break;
            case 'getPersonalApplicationImage':
                if (count($request) > 1) {
                    echo json_encode($get->getPersonalApplicationImage($request[1]));
                } else {
                    echo json_encode($get->getPersonalApplicationImage($data));
                }
                break;


            case 'getPersonalInquiry':
                if (count($request) > 1) {
                    echo json_encode($get->getPersonalInquiry($request[1]));
                } else {
                    echo json_encode($get->getPersonalInquiry($data));
                }
                break;

            case 'getPersonalTripping':
                if (count($request) > 1) {
                    echo json_encode($get->getPersonalTripping($request[1]));
                } else {
                    echo json_encode($get->getPersonalTripping($data));
                }
                break;

            case 'getPersonalCustomerCare':
                if (count($request) > 1) {
                    echo json_encode($get->getPersonalCustomerCare($request[1]));
                } else {
                    echo json_encode($get->getPersonalCustomerCare($data));
                }
                break;

            case 'getPersonalUnitTurnOver':
                if (count($request) > 1) {
                    echo json_encode($get->getPersonalUnitTurnover($request[1]));
                } else {
                    echo json_encode($get->getPersonalUnitTurnover($data));
                }
                break;

            case 'getTransferredEmails':
                if (count($request) > 1) {
                    echo json_encode($get->getTransferredUserEmails($request[1]));
                } else {
                    echo json_encode($get->getTransferredUserEmails());
                }
                break;

                case 'getUserPassword':
                    echo $get->getUserPassword($_GET['email']);
                    break;

                // HOA MANAGEMENT

            case 'getResidents':
                echo json_encode($get->getResidents());
                break;
            case 'getResidentStats':
                echo json_encode($get->getResidentStats());
                break;
            case 'getImage':
                echo json_encode($get->getImage());
                break;
            case 'getPayments':
                echo json_encode($get->getPayments());
                break;
            case 'getPaymentStats':
                echo json_encode($get->getPaymentStats());
                break;
            case 'getResidentUnits':
                echo json_encode($get->getResidentUnits());
                break;
            case 'getMaintenance':
                echo json_encode($get->getMaintenance());
                break;
            case 'getMaintenanceStats':
                echo json_encode($get->getMaintenanceStats());
                break;
            case 'getDocuments':
                echo json_encode($get->getDocuments());
                break;
            case 'getDocumentStats':
                echo json_encode($get->getDocumentStats());
                break;
            case 'getHoaEvents':
                echo json_encode($get->getEvents());
                break;
            case 'getEventStats':
                echo json_encode($get->getEventStats());
                break;
            case 'getPaymentTrends':
                echo json_encode($get->getPaymentTrends());
                break;
            case 'getUpcomingEvents':
                echo json_encode($get->getUpcomingEvents());
                break;
            case 'getCommunityStats':
                echo json_encode($get->getCommunityStats());
                break;


            default:
                // Return a 403 response for unsupported requests
                echo "This is forbidden";
                http_response_code(403);
                break;
        }
        break;
        // Handle POST requests    
    case 'POST':
        // Retrieves JSON-decoded data from php://input using file_get_contents
        $data = json_decode(file_get_contents("php://input"));
        switch ($request[0]) {
            case 'test':
                error_log("Test endpoint called");
                echo json_encode(["status" => "success", "message" => "Server is working"]);
                break;
            
            case 'login_users':
                error_log("login_users endpoint called");
                include_once __DIR__ . "/cors.php";
                echo $post->login_users($data);
                break;

            case 'Hoa_adminsignup':
                echo $post->HOA_adminSignup($data);
                break;
            case 'Hoa_adminlogin':
                echo $post->HOA_adminLogin($data);
                break;
                // END OF HOA CASES

                //USER HOME FINDER LOGIN SIGN UP
            case 'signup':
                echo $post->signup($data);
                break;

            case 'login':
                include_once __DIR__ . "/cors.php";
                echo $post->login($data);
                break;

                // HOME FINDER
            case 'admin_signup':
                echo $post->adminSignup($data);
                break;
            case 'admin_login':
                echo $post->adminLogin($data);
                break;

            case 'submitTrippingRequest':
                echo json_encode($post->submitTrippingRequest($data));
                break;

            case 'submitCustomerCare':
                echo json_encode($post->submitCustomerCare($data));
                break;

            case 'deleteTripping':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = json_decode(file_get_contents("php://input"), true);

                    // Log the received data for debugging
                    error_log('Received Data: ' . print_r($data, true));

                    if (!empty($data['id'])) {
                        echo json_encode($post->delete_tripping($data['id']));
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'ID is missing']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                }
                break;

            case 'submitCustomerCare':
                echo json_encode($post->submitCustomerCare($data));
                break;

            case 'deleteCustomercare':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = json_decode(file_get_contents("php://input"), true);

                    // Log the received data for debugging
                    error_log('Received Data: ' . print_r($data, true));

                    if (!empty($data['id'])) {
                        echo json_encode($post->delete_customerCare($data['id']));
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'ID is missing']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                }
                break;

            case 'submitTurnOver':
                echo json_encode($post->submitTurnOver($data));
                break;

            // Routines POST endpoints
            case 'validate-token':
                echo json_encode($post->validateAndRedeemToken($data));
                break;

            case 'submit-completion':
                echo json_encode($post->submitRoutineCompletion($_POST, $_FILES));
                break;

            case 'deleteTurnover':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = json_decode(file_get_contents("php://input"), true);

                    // Log the received data for debugging
                    error_log('Received Data: ' . print_r($data, true));

                    if (!empty($data['id'])) {
                        echo json_encode($post->delete_turnover($data['id']));
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'ID is missing']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                }
                break;

            case 'submitTurnover':
                echo json_encode($post->submitCustomerCare($data));
                break;

                // case 'send_appl': // Route for handling application submissions

                //     // Collect POST data and pass to the send_application method
                //     try {
                //         echo json_encode($post->send_application());
                //     } catch (Exception $e) {
                //         echo json_encode([
                //             'status' => 'error',
                //             'message' => $e->getMessage(),
                //         ]);
                //     }
                //     break;

                // case 'update_property':
                //     echo json_encode($post->update_property($data, $request[1]));
                //     break;

                // case 'delete_property':
                echo json_encode($post->delete_property($request[1]));
                break;

                // case 'update_property':
                //     if (count($request) > 1) {
                //         echo $post->update_property($data, $request[1]);
                //     } else {
                //         http_response_code(400);
                //         echo json_encode(["status" => "error", "message" => "Property ID is required"]);
                //     }
                //     break;

                // case 'delete_property':
                // if (count($request) > 1) {
                //     echo $post->delete_property($request[1]);
                // } else {
                //     http_response_code(400);
                //     echo json_encode(["status" => "error", "message" => "Property ID is required"]);
                // }
                // break;

                // NEED TO CALL THE FUNCTION

            case 'updateApplicationStatus':
                echo json_encode($post->updateApplicationStatus($data));
                break;

                //TILL THIS POINT

            case 'deleteEvents':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = json_decode(file_get_contents("php://input"), true);

                    // Log the received data for debugging
                    error_log('Received Data: ' . print_r($data, true));

                    if (!empty($data['id'])) {
                        echo json_encode($post->delete_events($data['id']));
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'ID is missing']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                }
                break;

            case 'updateCustomerCareStatus':
                echo json_encode($post->updateCustomerCareStatus($data));
                break;

            case 'updateTrippingStatus':
                echo json_encode($post->updateTrippingStatus($data));
                break;

            case 'updateTurnOverStatus':
                echo json_encode($post->updateTurnOverStatus($data));
                break;

            case 'updateInquiriesStatus':
                echo json_encode($post->updateInquiriesStatus($data));
                break;

            case 'transferUserEmails':
                echo json_encode($post->transferUserEmails($data));
                break;

            case 'addUser':
                echo $post->addUser($data);
                break;

            case 'addClass':
                echo json_encode($post->addClass($data));
                break;

            case 'generateTokens':
                echo json_encode($post->generateTokens($data));
                break;

            case 'editClass':
                echo json_encode($post->editClass($data));
                break;

            default:
                // Return a 403 response for unsupported requests
                echo "This is forbidden";
                http_response_code(403);
                break;
        }
        break;


    default:
        // Return a 404 response for unsupported HTTP methods
        echo "Method not available";
        http_response_code(404);
        break;
}
