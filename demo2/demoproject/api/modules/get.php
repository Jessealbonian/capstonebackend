<?php
require_once("global.php");

class Get extends GlobalMethods
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // TASKS
    public function get_tasks()
    {
        $sqlString = "SELECT task_id, user_id, title, description, date_due, time_due, image, status FROM task ORDER BY date_due DESC, time_due DESC";
        $result = $this->executeQuery($sqlString);

        if ($result['code'] == 200) {
            return $this->sendPayload($result['data'], "success", "Successfully retrieved tasks.", $result['code']);
        }

        return $this->sendPayload(null, "failed", "Failed to retrieve tasks.", $result['code']);
    }

    public function executeQuery($sql)
    {
        $data = array(); // place to store records retrieved for db
        $errmsg = ""; // initialized error message variable
        $code = 0; // initialize status code variable

        try {
            $statement = $this->pdo->query($sql);
            if ($statement) {
                $result = $statement->fetchAll();
                foreach ($result as $record) {
                    $data[] = $record;
                }
                $code = 200;
                return array("code" => $code, "data" => $data);
            } else {
                // if no record found, assign corresponding values to error messages/status
                $errmsg = "No records found";
                $code = 404;
            }
        } catch (\PDOException $e) {
            // PDO errors, mysql errors
            $errmsg = $e->getMessage();
            $code = 403;
        }
        return array("code" => $code, "errmsg" => $errmsg);
    }

    public function sendPayload($data, $remarks, $message, $code)
    {
        $status = array("remarks" => $remarks, "message" => $message);
        http_response_code($code);
        return array(
            "status" => $status,
            "payload" => $data,
            "prepared_by" => "Jomar",
            "timestamp" => date_create()
        );
    }

    public function get_records($table, $condition = null)
    {
        $sqlString = "SELECT * FROM $table";
        if ($condition != null) {
            $sqlString .= " WHERE " . $condition;
        }

        $result = $this->executeQuery($sqlString);

        if ($result['code'] == 200) {
            return $this->sendPayload($result['data'], "success", "Successfully retrieved records.", $result['code']);
        }

        return $this->sendPayload(null, "failed", "Failed to retrieve records.", $result['code']);
    }

    // public function get_employees($id = null)
    // {
    //     $condition = null;
    //     if ($id != null) {
    //         $condition = "EMPLOYEE_ID=$id";
    //     }
    //     return $this->get_records("employees", $condition);
    // }

    // public function getstatus($status)
    // {
    //     try {
    //         $sqlString = "SELECT * FROM todolist WHERE user_id = ?";
    //         $stmt = $this->pdo->prepare($sqlString);

    //         if (!$stmt) {
    //             return $this->getResponse(null, "Failed", "Failed to prepare statement", 500);
    //         }

    //         $stmt->bindParam(1, $status, PDO::PARAM_STR);

    //         if (!$stmt->execute()) {
    //             return $this->getResponse(null, "Failed", "Failed to execute statement", 500);
    //         }

    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         if ($result) {
    //             return $this->getResponse($result, "Success", null, 200);
    //         } else {
    //             return $this->getResponse(null, "Failed", "No records found", 404);
    //         }
    //     } catch (Exception $e) {
    //         return $this->getResponse(null, "Failed", "Exception: " . $e->getMessage(), 500);
    //     }
    // }

    // public function gettask($status)
    // {
    //     try {
    //         $sqlString = "SELECT * FROM todolist WHERE id = ?";
    //         $stmt = $this->pdo->prepare($sqlString);

    //         if (!$stmt) {
    //             return $this->getResponse(null, "Failed", "Failed to prepare statement", 500);
    //         }

    //         $stmt->bindParam(1, $status, PDO::PARAM_STR);

    //         if (!$stmt->execute()) {
    //             return $this->getResponse(null, "Failed", "Failed to execute statement", 500);
    //         }

    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         if ($result) {
    //             return $this->getResponse($result, "Success", null, 200);
    //         } else {
    //             return $this->getResponse(null, "Failed", "No records found", 404);
    //         }
    //     } catch (Exception $e) {
    //         return $this->getResponse(null, "Failed", "Exception: " . $e->getMessage(), 500);
    //     }
    // }

    // public function gettask_status($status)
    // {
    //     try {
    //         $sqlString = "SELECT * FROM todolist WHERE status = ?";
    //         $stmt = $this->pdo->prepare($sqlString);

    //         if (!$stmt) {
    //             return $this->getResponse(null, "Failed", "Failed to prepare statement", 500);
    //         }

    //         $stmt->bindParam(1, $status, PDO::PARAM_STR);

    //         if (!$stmt->execute()) {
    //             return $this->getResponse(null, "Failed", "Failed to execute statement", 500);
    //         }

    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         if ($result) {
    //             return $this->getResponse($result, "Success", null, 200);
    //         } else {
    //             return $this->getResponse(null, "Failed", "No records found", 404);
    //         }
    //     } catch (Exception $e) {
    //         return $this->getResponse(null, "Failed", "Exception: " . $e->getMessage(), 500);
    //     }
    // }


    // public function get_jobs($id = null)
    // {
    //     $condition = null;
    //     if ($id != null) {
    //         $condition = "JOB_ID=$id";
    //     }
    //     return $this->get_records("jobs", $condition);
    // }

    // HOME FUNCTIONS

    public function get_properties()
    {
        $sqlString = "SELECT * FROM properties"; // Adjust the table name as needed
        $result = $this->executeQuery($sqlString);

        if ($result['code'] == 200) {
            return $this->sendPayload($result['data'], "success", "Successfully retrieved properties.", $result['code']);
        }

        return $this->sendPayload(null, "failed", "Failed to retrieve properties.", $result['code']);
    }


    public function getMedia()
    {
        $news = $this->get_records("news"); // Assuming you have a 'news' table
        $events = $this->get_records("events"); // Assuming you have an 'events' table
        $blogs = $this->get_records("blogs"); // Assuming you have a 'blogs' table
        $vlogs = $this->get_records("vlogs"); // Assuming you have a 'vlogs' table

        return array(
            "news" => $news['payload'],
            "events" => $events['payload'],
            "blogs" => $blogs['payload'],
            "vlogs" => $vlogs['payload']
        );
    }

    public function get_Media($type)
    {
        $table = '';
        switch ($type) {
            case 'news':
                $table = 'news'; // Adjust the table name as needed
                break;
            case 'events':
                $table = 'events'; // Adjust the table name as needed
                break;
            case 'blogs':
                $table = 'blogs'; // Adjust the table name as needed
                break;
            case 'vlogs':
                $table = 'vlogs'; // Adjust the table name as needed
                break;
            default:
                return $this->sendPayload(null, "failed", "Invalid media type.", 400);
        }

        $result = $this->executeQuery("SELECT * FROM $table");

        if ($result['code'] == 200) {
            return $this->sendPayload($result['data'], "success", "Successfully retrieved $type.", $result['code']);
        }

        return $this->sendPayload(null, "failed", "Failed to retrieve $type.", $result['code']);
    }

    public function get_news()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM news ORDER BY date DESC");
            $stmt->execute();
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode([
                "status" => "success",
                "payload" => $news
            ]);
        } catch (\PDOException $e) {
            return json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function get_blogs()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM blogs ORDER BY date DESC");
            $stmt->execute();
            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode([
                "status" => "success",
                "payload" => $blogs
            ]);
        } catch (\PDOException $e) {
            return json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function get_Requests($type)
    {
        $table = '';
        switch ($type) {
            case 'inquiries':
                $table = 'inquiries'; // Adjust the table name as needed
                break;
            case 'tripping_request':
                $table = 'tripping_request'; // Adjust the table name as needed
                break;
            case 'customer_care':
                $table = 'customer_care'; // Adjust the table name as needed
                break;
            case 'unit_turnover':
                $table = 'unit_turnover'; // Adjust the table name as needed
                break;
            default:
                return $this->sendPayload(null, "failed", "Invalid request type.", 400);
        }

        $result = $this->executeQuery("SELECT * FROM $table");

        if ($result['code'] == 200) {
            return $this->sendPayload($result['data'], "success", "Successfully retrieved $type.", $result['code']);
        }

        return $this->sendPayload(null, "failed", "Failed to retrieve $type.", $result['code']);
    }

    public function get_adminss($id)
    {
        try {
            $sqlString = "SELECT * FROM admins WHERE admin_id = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $this->sendPayload($result, "Success", "Data retrieved successfully", 200);
            } else {
                return $this->sendPayload(null, "Error", "Failed to execute query", 500);
            }
        } catch (Exception $e) {
            return $this->sendPayload(null, "Error", "Exception occurred: " . $e->getMessage(), 500);
        }
    }

    // Public method to fetch vlogs
    public function get_vlogs()
    {
        try {

            $stmt = $this->pdo->prepare("SELECT * FROM vlogs ORDER BY created_at DESC");
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $vlogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([
                    "status" => "success",
                    "payload" => $vlogs
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "No vlogs found"
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "An error occurred: " . $e->getMessage()
            ]);
        }
    }


    // public function get_admins($id = null) {
    //     $condition = null;
    //     if ($id != null) {
    //         $condition = "admin_id=$id";
    //     }
    //     return $this->get_records("admins", $condition);
    // }


    public function getprof($id)
    {
        try {
            $sqlString = "SELECT * FROM admins WHERE admin_id = ?";
            $stmt = $this->pdo->prepare($sqlString);

            if (!$stmt) {
                return $this->getResponse(null, "Failed", "Failed to prepare statement", 500);
            }

            $stmt->bindParam(1, $id, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                return $this->getResponse(null, "Failed", "Failed to execute statement", 500);
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return $this->getResponse($result, "Success", null, 200);
            } else {
                return $this->getResponse(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->getResponse(null, "Failed", "Exception: " . $e->getMessage(), 500);
        }
    }


    public function getUserProf($id)
    {
        try {
            $sqlString = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sqlString);

            if (!$stmt) {
                return $this->getResponse(null, "Failed", "Failed to prepare statement", 500);
            }

            $stmt->bindParam(1, $id, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                return $this->getResponse(null, "Failed", "Failed to execute statement", 500);
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return $this->getResponse($result, "Success", null, 200);
            } else {
                return $this->getResponse(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->getResponse(null, "Failed", "Exception: " . $e->getMessage(), 500);
        }
    }

    public function getHoaUserProf($id)
    {
        try {
            $sqlString = "SELECT * FROM hoa_users WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sqlString);

            if (!$stmt) {
                return $this->getResponse(null, "Failed", "Failed to prepare statement", 500);
            }

            $stmt->bindParam(1, $id, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                return $this->getResponse(null, "Failed", "Failed to execute statement", 500);
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return $this->getResponse($result, "Success", null, 200);
            } else {
                return $this->getResponse(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->getResponse(null, "Failed", "Exception: " . $e->getMessage(), 500);
        }
    }

    public function getHoaAdminProf($id)
    {
        try {
            $sqlString = "SELECT * FROM hoa_admins WHERE admin_id = ?";
            $stmt = $this->pdo->prepare($sqlString);

            if (!$stmt) {
                return $this->getResponse(null, "Failed", "Failed to prepare statement", 500);
            }

            $stmt->bindParam(1, $id, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                return $this->getResponse(null, "Failed", "Failed to execute statement", 500);
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return $this->getResponse($result, "Success", null, 200);
            } else {
                return $this->getResponse(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->getResponse(null, "Failed", "Exception: " . $e->getMessage(), 500);
        }
    }

    public function getInquiries()
    {
        return $this->get_records("inquiries");
    }

    public function getTrippingRequests()
    {
        return $this->get_records("tripping_request");
    }

    public function getCustomerCareRequests()
    {
        return $this->get_records("customer_care");
    }

    public function getUnitTurnOvers()
    {
        return $this->get_records("unit_turnover");
    }

    public function getApplications()
    {
        try {
            $sqlString = "
                SELECT ua.*, u.email 
                FROM user_applications ua
                INNER JOIN users u ON ua.user_id = u.user_id
            ";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return $this->sendPayload($result, "Success", null, 200);
            } else {
                return $this->sendPayload(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->sendPayload(null, "Failed", "Failed to retrieve data", 500);
        }
    }

    public function getApprovedApplications()
    {
        try {
            $sqlString = "
                SELECT ua.*, u.email, u.username 
                FROM user_applications ua
                INNER JOIN users u ON ua.user_id = u.user_id
                WHERE ua.status = 'approved'
            ";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                return $this->sendPayload($result, "Success", "Approved applications retrieved successfully", 200);
            } else {
                return $this->sendPayload(null, "Failed", "No approved records found", 404);
            }
        } catch (PDOException $e) {
            return $this->getResponse(null, "Failed", "Error: " . $e->getMessage(), 500);
        }
    }


    // Add this new method to your Get class
    // public function getUserApplications($userId)
    // {
    //     try {
    //         $sqlString = "SELECT * FROM user_applications WHERE user_id = ?";
    //         $stmt = $this->pdo->prepare($sqlString);

    //         if (!$stmt) {
    //             return $this->sendPayload(null, "failed", "Failed to prepare statement", 500);
    //         }

    //         $stmt->bindParam(1, $userId, PDO::PARAM_INT);

    //         if (!$stmt->execute()) {
    //             return $this->sendPayload(null, "failed", "Failed to execute statement", 500);
    //         }

    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         if ($result) {
    //             return $this->sendPayload($result, "success", "Successfully retrieved user applications", 200);
    //         } else {
    //             return $this->sendPayload([], "success", "No applications found for this user", 200);
    //         }
    //     } catch (Exception $e) {
    //         return $this->sendPayload(null, "failed", "Exception: " . $e->getMessage(), 500);
    //     }
    // }

    public function getPersonalApplication($id)
    {
        try {
            if (!$id) {
                return $this->sendPayload(null, "Failed", "No user ID provided", 400);
            }
            $sqlString = "SELECT * FROM user_applications WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                return $this->sendPayload($result, "Success", null, 200);
            } else {
                return $this->sendPayload(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->sendPayload(null, "Failed", "Failed to retrive data", 500);
        }
    }

    public function getPersonalApplicationImage($appl_id)
    {
        try {
            if (!$appl_id) {
                return $this->sendPayload(null, "Failed", "No application ID provided", 400);
            }
    
            $sqlString = "
                SELECT ua.*, u.email 
                FROM user_applications ua
                INNER JOIN users u ON ua.user_id = u.user_id
                WHERE ua.appl_id = ?
            ";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(1, $appl_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if ($result) {
                return $this->sendPayload($result, "Success", null, 200);
            } else {
                return $this->sendPayload(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->sendPayload(null, "Failed", "Failed to retrieve data", 500);
        }
    }
    
    public function getPersonalInquiry($id)
    {
        try {
            if (!$id) {
                return $this->sendPayload(null, "Failed", "No user ID provided", 400);
            }
            $sqlString = "SELECT * FROM inquiries WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                return $this->sendPayload($result, "Success", null, 200);
            } else {
                return $this->sendPayload(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->sendPayload(null, "Failed", "Failed to retrive data", 500);
        }
    }

    public function getPersonalTripping($id)
    {
        try {
            if (!$id) {
                return $this->sendPayload(null, "Failed", "No user ID provided", 400);
            }
            $sqlString = "SELECT * FROM tripping_request WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                return $this->sendPayload($result, "Success", null, 200);
            } else {
                return $this->sendPayload(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->sendPayload(null, "Failed", "Failed to retrive data", 500);
        }
    }
public function getPersonalCustomerCare($id)
    {
        try {
            if (!$id) {
                return $this->sendPayload(null, "Failed", "No user ID provided", 400);
            }
            $sqlString = "SELECT * FROM customer_care WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                return $this->sendPayload($result, "Success", null, 200);
            } else {
                return $this->sendPayload(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->sendPayload(null, "Failed", "Failed to retrive data", 500);
        }
    }

    public function getPersonalUnitTurnover($id)
    {
        try {
            if (!$id) {
                return $this->sendPayload(null, "Failed", "No user ID provided", 400);
            }
            $sqlString = "SELECT * FROM unit_turnover WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                return $this->sendPayload($result, "Success", null, 200);
            } else {
                return $this->sendPayload(null, "Failed", "No records found", 404);
            }
        } catch (Exception $e) {
            return $this->sendPayload(null, "Failed", "Failed to retrive data", 500);
        }
    }

    // ACCOUNTS INTEGRATION GETTING
    public function getTransferredUserEmails()
    {
        try {
            $sqlString = "
                SELECT ua.*, u.email, u.username, u.password 
                FROM user_applications ua
                INNER JOIN users u ON ua.user_id = u.user_id
                WHERE ua.email_transfer = 'sent'
            ";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                return $this->sendPayload($result, "Success", "Sent emails retrieved successfully", 200);
            } else {
                return $this->sendPayload(null, "Failed", "No transferred records found", 404);
            }
        } catch (PDOException $e) {
            return $this->getResponse(null, "Failed", "Error: " . $e->getMessage(), 500);
        }
    }
    
    public function getUserPassword($email)
{
    $stmt = $this->pdo->prepare("SELECT password FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode(['password' => $user['password']]);
    } else {
        http_response_code(404);
        return json_encode(['message' => 'User not found']);
    }
}
    


    // HOA MANAGEMENT FUNCTIONS
    public function getImage()
    {
        try {
            $sql = "SELECT * FROM images";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved images.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve images: " . $e->getMessage()
            ];
        }
    }

    public function getResidents()
    {
        try {
            $sql = "SELECT * FROM residents ORDER BY created_at DESC";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved residents.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve residents: " . $e->getMessage()
            ];
        }
    }

    public function getResidentStats()
    {
        try {
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending
                    FROM residents";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved resident statistics.",
                "data" => [
                    "total" => (int)$result['total'],
                    "active" => (int)$result['active'],
                    "pending" => (int)$result['pending']
                ]
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve resident statistics: " . $e->getMessage()
            ];
        }
    }

    public function getPayments()
    {
        try {
            $sql = "SELECT * FROM payments ORDER BY date DESC";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved payments.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve payments: " . $e->getMessage()
            ];
        }
    }

    public function getPaymentStats()
    {
        try {
            $sql = "SELECT 
                    SUM(amount) as total_collections,
                    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as pending_amount,
                    SUM(CASE WHEN status = 'Overdue' THEN amount ELSE 0 END) as overdue_amount
                    FROM payments";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved payment statistics.",
                "data" => [
                    "total_collections" => (float)$result['total_collections'] ?? 0,
                    "pending_amount" => (float)$result['pending_amount'] ?? 0,
                    "overdue_amount" => (float)$result['overdue_amount'] ?? 0
                ]
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve payment statistics: " . $e->getMessage()
            ];
        }
    }

    public function getResidentUnits()
    {
        try {
            $sql = "SELECT unit, name FROM residents WHERE status = 'Active'";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved resident units.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve resident units: " . $e->getMessage()
            ];
        }
    }

    public function getMaintenance()
    {
        try {
            $sql = "SELECT * FROM maintenance ORDER BY request_date DESC";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved maintenance requests.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve maintenance requests: " . $e->getMessage()
            ];
        }
    }

    public function getMaintenanceStats()
    {
        try {
            $sql = "SELECT 
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as open_requests,
                    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'Completed' 
                        AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                        THEN 1 ELSE 0 END) as completed_this_week
                    FROM maintenance";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Convert null values to 0
            $data = [
                "open_requests" => (int)($result['open_requests'] ?? 0),
                "in_progress" => (int)($result['in_progress'] ?? 0),
                "completed_this_week" => (int)($result['completed_this_week'] ?? 0)
            ];

            return [
                "status" => "success",
                "message" => "Successfully retrieved maintenance statistics.",
                "data" => $data
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve maintenance statistics: " . $e->getMessage()
            ];
        }
    }

    public function getDocuments()
    {
        try {
            $sql = "SELECT * FROM documents ORDER BY last_updated DESC";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved documents.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve documents: " . $e->getMessage()
            ];
        }
    }

    public function getDocumentStats()
    {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(CASE WHEN type = 'Rule' THEN 1 ELSE 0 END) as rules_count,
                    COUNT(DISTINCT CASE WHEN type = 'Form' THEN id END) as forms_count
                    FROM documents";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved document statistics.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve document statistics: " . $e->getMessage()
            ];
        }
    }

    public function getEvents()
    {
        try {
            $sql = "SELECT * FROM hoa_events";
            $params = [];

            // If year and month are provided, filter by them
            if (isset($_GET['year']) && isset($_GET['month'])) {
                $sql .= " WHERE YEAR(date) = ? AND MONTH(date) = ?";
                $params = [$_GET['year'], $_GET['month']];
            }

            $sql .= " ORDER BY date, time";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add full image URL path to each event
            foreach ($result as &$event) {
                if ($event['image']) {
                    // Check if image is already a full URL
                    if (!filter_var($event['image'], FILTER_VALIDATE_URL)) {
                        $event['image'] = $this->getImageUrl($event['image']);
                    }
                }
            }

            return [
                "status" => "success",
                "message" => "Successfully retrieved events.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve events: " . $e->getMessage()
            ];
        }
    }

    public function getEventStats()
    {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_hoa_events,
                    SUM(CASE WHEN status = 'Upcoming' THEN 1 ELSE 0 END) as upcoming_events,
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_events,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_events,
                    SUM(attendees) as total_attendees
                    FROM hoa_events";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved event statistics.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve event statistics: " . $e->getMessage()
            ];
        }
    }

    public function getPaymentTrends()
    {
        try {
            $sql = "SELECT month, collections, outstanding 
                    FROM payment_trends 
                    ORDER BY year, FIELD(month, 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec') 
                    LIMIT 6";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $months = [];
            $collections = [];
            $outstanding = [];

            foreach ($result as $row) {
                $months[] = $row['month'];
                $collections[] = floatval($row['collections']);
                $outstanding[] = floatval($row['outstanding']);
            }

            return [
                "status" => "success",
                "message" => "Successfully retrieved payment trends.",
                "data" => [
                    "months" => $months,
                    "collections" => $collections,
                    "outstanding" => $outstanding
                ]
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve payment trends: " . $e->getMessage()
            ];
        }
    }

    public function getUpcomingEvents()
    {
        try {
            $sql = "SELECT title, date, time 
                    FROM hoa_events 
                    WHERE status = 'Upcoming' 
                    AND date >= CURDATE() 
                    ORDER BY date ASC, time ASC 
                    LIMIT 3";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved upcoming events.",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve upcoming events: " . $e->getMessage()
            ];
        }
    }

    public function getCommunityStats()
    {
        try {
            $sql = "SELECT 
                    (SELECT COUNT(*) FROM residents) as total_units,
                    (SELECT COUNT(*) FROM residents WHERE status = 'Active') as occupied_units,
                    (SELECT 
                        CASE 
                            WHEN COUNT(*) > 0 
                            THEN (COUNT(CASE WHEN status = 'Paid' THEN 1 END) * 100 / COUNT(*))
                            ELSE 0 
                        END
                    FROM payments 
                    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    ) as payment_compliance
                    FROM dual";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "message" => "Successfully retrieved community stats.",
                "data" => [
                    "total_units" => (int)($result['total_units'] ?? 0),
                    "occupied_units" => (int)($result['occupied_units'] ?? 0),
                    "payment_compliance" => (int)($result['payment_compliance'] ?? 0)
                ]
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to retrieve community stats: " . $e->getMessage()
            ];
        }
    }

    // Add this helper function to the Get class
    private function getImageUrl($imagePath)
    {
        // Base URL should match your project structure
        $baseUrl = "http://localhost/DEMO2/demoproject/api/uploads/events/";
        return $baseUrl . $imagePath;
    }

    public function getClasses() {
        try {
            $sql = "SELECT * FROM class_routines ORDER BY class_id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                "status" => "success",
                "data" => $classes
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to fetch classes: " . $e->getMessage()
            ];
        }
    }

    // Fetch attendance list for a specific class and date
    public function getClassAttendance($classId, $year, $month, $day) {
        try {
            if (!$classId || !$year || !$month || !$day) {
                return $this->sendPayload(null, "failed", "Missing required parameters.", 400);
            }

            $date = sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day);

            // Read attendance from routine_history for the given class and date
            $sql = "SELECT rh.user_id, u.username AS name, rh.img AS img, rh.routine, rh.routine_intensity, rh.time_of_submission
                    FROM routine_history rh
                    INNER JOIN hoa_users u ON u.user_id = rh.user_id
                    WHERE rh.class_id = :class_id AND rh.date_of_submission = :attend_date";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
            $stmt->bindParam(':attend_date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->sendPayload($rows, "success", "Successfully retrieved attendance.", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failed to fetch attendance: " . $e->getMessage(), 500);
        }
    }

    // Routines methods
    public function getEnrolledClasses($studentUsername) {
        try {
            // This method kept for backward-compatibility but now requires user_id via query string
            if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
                return $this->sendPayload([], "failed", "user_id is required", 400);
            }
            $userId = (int)$_GET['user_id'];

            $sql = "SELECT DISTINCT cg.class_id AS id, cg.Requestedbycoach AS coach_username
                    FROM codegen cg
                    WHERE cg.user_id = :user_id AND cg.class_id IS NOT NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add a friendly title derived from class_id to match frontend expectations
            $classes = array_map(function ($r) {
                $r['title'] = 'Class ' . $r['id'];
                return $r;
            }, $rows);

            return $this->sendPayload($classes, "success", "Successfully retrieved enrolled classes.", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failed to retrieve enrolled classes: " . $e->getMessage(), 500);
        }
    }

    // New: enrolled classes using only user_id from path
    public function getEnrolledClassesById($userId) {
        try {
            if (!$userId || !is_numeric($userId)) {
                return $this->sendPayload([], "failed", "user_id is required", 400);
            }
            $uid = (int)$userId;

            $sql = "SELECT DISTINCT cg.class_id AS id, cg.Requestedbycoach AS coach_username
                    FROM codegen cg
                    WHERE cg.user_id = :user_id AND cg.class_id IS NOT NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $uid, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $classes = array_map(function ($r) {
                $r['title'] = 'Class ' . $r['id'];
                return $r;
            }, $rows);

            return $this->sendPayload($classes, "success", "Successfully retrieved enrolled classes.", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failedd to retrieve enrolled classes: " . $e->getMessage(), 500);
        }
    }

    public function getAllEnrolledClasses() {
        try {
            $sql = "SELECT DISTINCT cg.class_id AS id, cg.Requestedbycoach AS coach_username, cg.user_id
                    FROM codegen cg
                    WHERE cg.class_id IS NOT NULL";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $classes = array_map(function ($r) {
                $r['title'] = 'Class ' . $r['id'];
                return $r;
            }, $rows);

            return $this->sendPayload($classes, "success", "Successfully retrieved all enrolled classes.", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failed to retrieve all enrolled classes: " . $e->getMessage(), 500);
        }
    }

    public function getClassRoutines($classId) {
        try {
            // Support weekly schema: columns like mondayRoutine, mondayintensity, ...
            $sql = "SELECT class_id, class_name, description,
                           mondayRoutine, tuesdayRoutine, wednesdayRoutine, thursdayRoutine, fridayRoutine, saturdayRoutine, sundayRoutine,
                           mondayintensity, tuesdayintensity, wednesdayintensity, thursdayintensity, fridayintensity, saturdayintensity, sundayintensity
                    FROM class_routines
                    WHERE class_id = :class_id
                    ORDER BY class_id DESC LIMIT 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return $this->sendPayload([], "success", "No routines found for class", 200);
            }

            $weekly = [
                'Monday' => [ 'task' => $row['mondayRoutine'] ?? '', 'intensity' => $row['mondayintensity'] ?? '' ],
                'Tuesday' => [ 'task' => $row['tuesdayRoutine'] ?? '', 'intensity' => $row['tuesdayintensity'] ?? '' ],
                'Wednesday' => [ 'task' => $row['wednesdayRoutine'] ?? '', 'intensity' => $row['wednesdayintensity'] ?? '' ],
                'Thursday' => [ 'task' => $row['thursdayRoutine'] ?? '', 'intensity' => $row['thursdayintensity'] ?? '' ],
                'Friday' => [ 'task' => $row['fridayRoutine'] ?? '', 'intensity' => $row['fridayintensity'] ?? '' ],
                'Saturday' => [ 'task' => $row['saturdayRoutine'] ?? '', 'intensity' => $row['saturdayintensity'] ?? '' ],
                'Sunday' => [ 'task' => $row['sundayRoutine'] ?? '', 'intensity' => $row['sundayintensity'] ?? '' ],
            ];

            $payload = [
                'class_id' => (int)$row['class_id'],
                'title' => $row['class_name'] ?? null,
                'description' => $row['description'] ?? null,
                'weekly' => $weekly
            ];

            return $this->sendPayload($payload, "success", "Successfully retrieved class routines.", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failed to retrieve class routines: " . $e->getMessage(), 500);
        }
    }

    // Return class meta info (title/description/coach) for a class_id
    public function getClassInfoById($classId) {
        try {
            $coach = null;
            $coachStmt = $this->pdo->prepare("SELECT Requestedbycoach FROM codegen WHERE class_id = :class_id ORDER BY code_id DESC LIMIT 1");
            $coachStmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
            $coachStmt->execute();
            $cgRow = $coachStmt->fetch(PDO::FETCH_ASSOC);
            if ($cgRow && isset($cgRow['Requestedbycoach'])) {
                $coach = $cgRow['Requestedbycoach'];
            }

            $stmt = $this->pdo->prepare("SELECT * FROM class_routines WHERE class_id = :class_id LIMIT 1");
            $stmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            $payload = [
                'id' => (int)$classId,
                'title' => $row['class_name'] ?? $row['title'] ?? ('Class ' . (int)$classId),
                'description' => $row['class_description'] ?? ($row['description'] ?? null),
                'coach_username' => $coach
            ];

            return $this->sendPayload($payload, 'success', 'Class info fetched', 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, 'failed', 'Failed to fetch class info: ' . $e->getMessage(), 500);
        }
    }

    // Fetch all routines for all classes redeemed by a given user (via username or user_id)
    public function getUserClassRoutines($studentUsername) {
        try {
            if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
                return $this->sendPayload([], "failed", "user_id is required", 400);
            }
            $userId = (int)$_GET['user_id'];

            // 2) Get all class_ids from codegen redeemed by this user
            $cgStmt = $this->pdo->prepare("SELECT DISTINCT class_id FROM codegen WHERE user_id = :user_id AND class_id IS NOT NULL");
            $cgStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $cgStmt->execute();
            $classRows = $cgStmt->fetchAll(PDO::FETCH_COLUMN, 0);

            if (!$classRows || count($classRows) === 0) {
                return $this->sendPayload([], "success", "No enrolled classes", 200);
            }

            // 3) Fetch all routines from class_routines for those class_ids
            $placeholders = implode(',', array_fill(0, count($classRows), '?'));
            $sql = "SELECT id, class_id, task_title, task_description, due_date, status
                    FROM class_routines
                    WHERE class_id IN ($placeholders)
                    ORDER BY class_id ASC, due_date ASC";
            $stmt = $this->pdo->prepare($sql);
            foreach ($classRows as $index => $cid) {
                $stmt->bindValue($index + 1, (int)$cid, PDO::PARAM_INT);
            }
            $stmt->execute();
            $routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->sendPayload($routines, "success", "Successfully retrieved user class routines.", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failed to retrieve user class routines: " . $e->getMessage(), 500);
        }
    }

    // New: user-class-routines using only user_id from path
    public function getUserClassRoutinesById($userId) {
        try {
            if (!$userId || !is_numeric($userId)) {
                return $this->sendPayload([], "failed", "user_id is required", 400);
            }
            $uid = (int)$userId;

            $cgStmt = $this->pdo->prepare("SELECT DISTINCT class_id FROM codegen WHERE user_id = :user_id AND class_id IS NOT NULL");
            $cgStmt->bindParam(':user_id', $uid, PDO::PARAM_INT);
            $cgStmt->execute();
            $classRows = $cgStmt->fetchAll(PDO::FETCH_COLUMN, 0);

            if (!$classRows || count($classRows) === 0) {
                return $this->sendPayload([], "success", "No enrolled classes", 200);
            }

            $placeholders = implode(',', array_fill(0, count($classRows), '?'));
            $sql = "SELECT id, class_id, task_title, task_description, due_date, status
                    FROM class_routines
                    WHERE class_id IN ($placeholders)
                    ORDER BY class_id ASC, due_date ASC";
            $stmt = $this->pdo->prepare($sql);
            foreach ($classRows as $index => $cid) {
                $stmt->bindValue($index + 1, (int)$cid, PDO::PARAM_INT);
            }
            $stmt->execute();
            $routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->sendPayload($routines, "success", "Successfully retrieved user class routines.", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failed to retrieve user class routines: " . $e->getMessage(), 500);
        }
    }

    public function getRoutineHistory($studentUsername) {
        try {
            $sql = "SELECT rh.id, rh.class_id, rh.user_id, rh.routine, rh.routine_intensity, rh.time_of_submission, rh.date_of_submission, rh.img
                    FROM routine_history rh
                    INNER JOIN hoa_users u ON rh.user_id = u.user_id
                    WHERE u.username = :student_username
                    ORDER BY rh.date_of_submission DESC, rh.time_of_submission DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':student_username', $studentUsername, PDO::PARAM_STR);
            $stmt->execute();
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->sendPayload($history, "success", "Successfully retrieved routine history.", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failed to retrieve routine history: " . $e->getMessage(), 500);
        }
    }

    public function checkTodayRoutine($routineId, $studentUsername) {
        try {
            $today = date('Y-m-d');
            $sql = "SELECT rh.id 
                    FROM routine_history rh
                    INNER JOIN hoa_users u ON rh.user_id = u.user_id
                    WHERE rh.class_id = :class_id 
                    AND u.username = :student_username 
                    AND DATE(rh.date_of_submission) = :today";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':class_id', $routineId, PDO::PARAM_INT);
            $stmt->bindParam(':student_username', $studentUsername, PDO::PARAM_STR);
            $stmt->bindParam(':today', $today, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $this->sendPayload(
                ['completed' => !empty($result)], 
                "success", 
                "Successfully checked today's routine.", 
                200
            );
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", "Failed to check today's routine: " . $e->getMessage(), 500);
        }
    }
}
