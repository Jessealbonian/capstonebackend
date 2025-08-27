<?php

/**
 * Post Class
 *
 * This PHP class provides methods for adding employees and jobs.
 *
 * Usage:
 * 1. Include this class in your project.
 * 2. Create an instance of the class to access the provided methods.
 * 3. Call the appropriate method to add new employees or jobs with the provided data.
 *
 * Example Usage:
 * ```
 * $post = new Post();
 * $employeeData = ... // prepare employee data as an associative array or object
 * $addedEmployee = $post->add_employees($employeeData);
 *
 * $jobData = ... // prepare job data as an associative array or object
 * $addedJob = $post->add_jobs($jobData);
 * ```
 *
 * Note: Customize the methods as needed to handle the addition of data to your actual data source (e.g., database, API).
 */

use Firebase\JWT\JWT;

require_once("global.php");
require 'vendor/autoload.php';
class Post extends GlobalMethods
{

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // HOA LOGIN AND SIGNUP
    public function HOA_adminSignup($data)
    {
        $secret_key = "63448c0f19663276ceabdc626d7aab8855872cc7ef5b152d099c41dcbbccd4ce";
        $issuer_claim = "http://localhost";
        $audience_claim = "http://localhost";
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + 3600;

        if (isset($data->username) && isset($data->email) && isset($data->password)) {
            $username = $data->username;
            $email = $data->email;
            $password = password_hash($data->password, PASSWORD_BCRYPT);

            // Generate or fetch admin_code
            $admin_code = isset($data->admin_code) ? $data->admin_code : uniqid("admin_");

            $stmt = $this->pdo->prepare("SELECT * FROM hoa_admins WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(409);
                return json_encode(["message" => "Email already exists"]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO hoa_admins (username, email, password, admin_code) VALUES (:username, :email, :password, :admin_code)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':admin_code', $admin_code);

                if ($stmt->execute()) {
                    $user_id = $this->pdo->lastInsertId();
                    $token = array(
                        "iss" => $issuer_claim,
                        "aud" => $audience_claim,
                        "iat" => $issuedat_claim,
                        "nbf" => $notbefore_claim,
                        "exp" => $expire_claim,
                        "data" => array(
                            "id" => $user_id,
                            "username" => $username,
                            "email" => $email,
                            "admin_code" => $admin_code
                        )
                    );

                    $jwt = JWT::encode($token, $secret_key, 'HS256');
                    return json_encode(
                        array(
                            "message" => "Signup successful",
                            "jwt" => $jwt,
                            "username" => $username,
                            "email" => $email,
                            "admin_code" => $admin_code,
                            "expireAt" => $expire_claim
                        )
                    );
                } else {
                    http_response_code(500);
                    return json_encode(["message" => "Internal server error"]);
                }
            }
        } else {
            http_response_code(400);
            return json_encode(["message" => "Username, email, and password are required"]);
        }
    }

    // TASKS
    public function add_task($data)
    {
        try {
            if (!isset($data->title) || !isset($data->status)) {
                return [
                    "status" => "error",
                    "message" => "Title and status are required"
                ];
            }

            $sql = "INSERT INTO task (
                        user_id,
                        title,
                        description,
                        date_due,
                        time_due,
                        image,
                        status
                    ) VALUES (
                        :user_id,
                        :title,
                        :description,
                        :date_due,
                        :time_due,
                        :image,
                        :status
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $data->user_id ?? null,
                ':title' => trim($data->title),
                ':description' => isset($data->description) && $data->description !== '' ? trim($data->description) : null,
                ':date_due' => isset($data->date_due) && $data->date_due !== '' ? trim($data->date_due) : null,
                ':time_due' => isset($data->time_due) && $data->time_due !== '' ? trim($data->time_due) : null,
                ':image' => isset($data->image) && $data->image !== '' ? trim($data->image) : null,
                ':status' => trim($data->status)
            ]);

            return [
                "status" => "success",
                "message" => "Task added successfully",
                "task_id" => $this->pdo->lastInsertId()
            ];
        } catch (\PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to add task: " . $e->getMessage()
            ];
        }
    }

    public function update_task($data)
    {
        try {
            // Support both JSON (application/json) and multipart/form-data
            $taskId = null;
            if (is_object($data) && isset($data->task_id)) {
                $taskId = $data->task_id;
            }
            if (isset($_POST['task_id'])) {
                $taskId = $_POST['task_id'];
            }
            if (!$taskId) {
                return [
                    "status" => "error",
                    "message" => "task_id is required"
                ];
            }

            // If multipart form (screenshot for completion)
            if (!empty($_FILES['image']) && isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $uploadDir = __DIR__ . '/../uploads/tasks/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = uniqid('task_') . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    return [
                        "status" => "error",
                        "message" => "Failed to upload image"
                    ];
                }

                // Store relative path accessible from server
                $relativePath = 'uploads/tasks/' . $fileName;

                $sql = "UPDATE task SET image = :image, status = :status WHERE task_id = :task_id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':image' => $relativePath,
                    ':status' => isset($_POST['status']) ? trim($_POST['status']) : 'Completed',
                    ':task_id' => $taskId
                ]);

                return [
                    "status" => "success",
                    "message" => "Task updated with screenshot"
                ];
            }

            // JSON or simple form update (no image)
            $sql = "UPDATE task SET status = :status WHERE task_id = :task_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':status' => (is_object($data) && isset($data->status)) ? trim($data->status) : (isset($_POST['status']) ? trim($_POST['status']) : 'Pending'),
                ':task_id' => $taskId
            ]);

            return [
                "status" => "success",
                "message" => "Task updated successfully"
            ];
        } catch (\PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to update task: " . $e->getMessage()
            ];
        }
    }

    public function HOA_adminLogin($data)
    {
        $secret_key = "63448c0f19663276ceabdc626d7aab8855872cc7ef5b152d099c41dcbbccd4ce";
        $issuer_claim = "http://localhost";
        $audience_claim = "http://localhost";
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + 3600;

        if (isset($data->email) && isset($data->password)) {
            $email = htmlspecialchars(strip_tags($data->email)); // Sanitize input
            $password = $data->password;

            // Check if the user exists
            $stmt = $this->pdo->prepare("SELECT * FROM hoa_admins WHERE email = :email");
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    $token = array(
                        "iss" => $issuer_claim,
                        "aud" => $audience_claim,
                        "iat" => $issuedat_claim,
                        "nbf" => $notbefore_claim,
                        "exp" => $expire_claim,
                        "data" => array(
                            "id" => $user['admin_id'],
                            "email" => $user['email'],
                            "admin_code" => $user['admin_code'],
                        )
                    );

                    $jwt = JWT::encode($token, $secret_key, 'HS256');

                    return json_encode(array(
                        "message" => "Login successful",
                        "jwt" => $jwt,
                        "id" => $user['admin_id'],
                        "email" => $email,
                        "expireAt" => $expire_claim
                    ));
                }
            }

            // Invalid credentials or execution error
            http_response_code(401);
            return json_encode(array("message" => "Invalid email or password"));
        } else {
            http_response_code(400);
            return json_encode(array("message" => "Email and password are required"));
        }
    }

    public function login_users($data)
    {
        $secret_key = "63448c0f19663276ceabdc626d7aab8855872cc7ef5b152d099c41dcbbccd4ce"; // Use your secret key here
        $issuer_claim = "http://localhost";
        $audience_claim = "http://localhost";
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + 3600;

        if (isset($data->email) && isset($data->password)) {
            $email = $data->email;
            $password = $data->password;

            $stmt = $this->pdo->prepare("SELECT * FROM hoa_users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => $user['user_id'],
                        "email" => $user['email']
                    )
                );

                $jwt = JWT::encode($token, $secret_key, 'HS256');

                return json_encode(array(
                    "message" => "Login successful",
                    "jwt" => $jwt,
                    "id" => $user['user_id'],
                    "email" => $email,
                    "expireAt" => $expire_claim
                ));
            } else {
                http_response_code(401);
                return json_encode(array("message" => "Invalid email or password"));
            }
        } else {
            http_response_code(400);
            return json_encode(array("message" => "Email and password are required"));
        }
    }

    public function signup_users($data)
    {
        $secret_key = "63448c0f19663276ceabdc626d7aab8855872cc7ef5b152d099c41dcbbccd4ce";
        $issuer_claim = "http://localhost";
        $audience_claim = "http://localhost";
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + 3600;

        if (isset($data->username) && isset($data->email) && isset($data->password)) {
            $username = $data->username;
            $email = $data->email;
            $password = password_hash($data->password, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare("SELECT * FROM hoa_users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(409);
                return json_encode(["message" => "Email already exists"]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO hoa_users (username, email, password) VALUES (:username, :email, :password)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);

                if ($stmt->execute()) {
                    $user_id = $this->pdo->lastInsertId();
                    $token = array(
                        "iss" => $issuer_claim,
                        "aud" => $audience_claim,
                        "iat" => $issuedat_claim,
                        "nbf" => $notbefore_claim,
                        "exp" => $expire_claim,
                        "data" => array(
                            "id" => $user_id,
                            "username" => $username,
                            "email" => $email
                        )
                    );

                    $jwt = JWT::encode($token, $secret_key, 'HS256');
                    return json_encode(
                        array(
                            "message" => "Signup successful",
                            "jwt" => $jwt,
                            "username" => $username,
                            "email" => $email,
                            "expireAt" => $expire_claim
                        )
                    );
                } else {
                    http_response_code(500);
                    return json_encode(["message" => "Internal server error"]);
                }
            }
        } else {
            http_response_code(400);
            return json_encode(["message" => "Username, email, and password are required"]);
        }
    }
    // END OF HOA FUNCTIONS

    public function login($data)
    {
        $secret_key = "63448c0f19663276ceabdc626d7aab8855872cc7ef5b152d099c41dcbbccd4ce"; // Use your secret key here
        $issuer_claim = "http://localhost";
        $audience_claim = "http://localhost";
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + 3600;

        if (isset($data->email) && isset($data->password)) {
            $email = $data->email;
            $password = $data->password;

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => $user['user_id'],
                        "email" => $user['email']
                    )
                );

                $jwt = JWT::encode($token, $secret_key, 'HS256');

                return json_encode(array(
                    "message" => "Login successful",
                    "jwt" => $jwt,
                    "id" => $user['user_id'],
                    "email" => $email,
                    "expireAt" => $expire_claim
                ));
            } else {
                http_response_code(401);
                return json_encode(array("message" => "Invalid email or password"));
            }
        } else {
            http_response_code(400);
            return json_encode(array("message" => "Email and password are required"));
        }
    }

    public function signup($data)
    {
        $secret_key = "63448c0f19663276ceabdc626d7aab8855872cc7ef5b152d099c41dcbbccd4ce";
        $issuer_claim = "http://localhost";
        $audience_claim = "http://localhost";
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + 3600;

        if (isset($data->username) && isset($data->email) && isset($data->password)) {
            $username = $data->username;
            $email = $data->email;
            $password = password_hash($data->password, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(409);
                return json_encode(["message" => "Email already exists"]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);

                if ($stmt->execute()) {
                    $user_id = $this->pdo->lastInsertId();
                    $token = array(
                        "iss" => $issuer_claim,
                        "aud" => $audience_claim,
                        "iat" => $issuedat_claim,
                        "nbf" => $notbefore_claim,
                        "exp" => $expire_claim,
                        "data" => array(
                            "id" => $user_id,
                            "username" => $username,
                            "email" => $email
                        )
                    );

                    $jwt = JWT::encode($token, $secret_key, 'HS256');
                    return json_encode(
                        array(
                            "message" => "Signup successful",
                            "jwt" => $jwt,
                            "username" => $username,
                            "email" => $email,
                            "expireAt" => $expire_claim
                        )
                    );
                } else {
                    http_response_code(500);
                    return json_encode(["message" => "Internal server error"]);
                }
            }
        } else {
            http_response_code(400);
            return json_encode(["message" => "Username, email, and password are required"]);
        }
    }

    // public function add_task($data, $id)
    // {
    //     $errrmsg = "";
    //     $code = 0;
    //     $sql = "INSERT INTO todolist(
    //             task, description, due_date, status, user_id)
    //             VALUES (?,?,?,?,?)
    //             ";
    //     try {
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute(
    //             [

    //                 $data->task,
    //                 $data->description,
    //                 $data->due_date,
    //                 $data->status,
    //                 $id,


    //             ]
    //         );
    //         return $this->sendPayload(null, "success", "succesfully updated data", $code);
    //     } catch (\PDOException $e) {
    //         $errmsg = $e->getMessage();
    //         $code = 400;
    //     }
    //     return $this->sendPayload(null, "failed", $errmsg, $code);
    // }

    // public function edit_taskk($data, $id)
    // {
    //     $errrmsg = "";
    //     $code = 0;
    //     $sql = "UPDATE todolist set task=?, description=?, due_date=?, status=?
    //                 WHERE id=?";

    //     try {
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute(
    //             [
    //                 $data->task,
    //                 $data->description,
    //                 $data->due_date,
    //                 $data->status,
    //                 $id,
    //             ]
    //         );
    //         return $this->sendPayload(null, "success", "succesfully updated data", $code);
    //     } catch (\PDOException $e) {
    //         $errmsg = $e->getMessage();
    //         $code = 400;
    //     }
    //     return $this->sendPayload(null, "failed", $errmsg, $code);
    // }

    // public function delete_employees($id)
    // {
    //     $errmsg = "";
    //     $code = 0;
    //     $sql = "DELETE FROM todolist WHERE id=?";

    //     try {
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute(
    //             [
    //                 $id
    //             ]
    //         );
    //         return $this->sendPayload(null, "success", "Successfully deleted employee", $code);
    //     } catch (\PDOException $e) {
    //         $errmsg = $e->getMessage();
    //         $code = 400;
    //     }
    //     return $this->sendPayload(null, "failed", $errmsg, $code);
    // }

    // public function update_task_status($taskId, $status)
    // {
    //     $errmsg = "";
    //     $code = 0;
    //     $sql = "UPDATE todolist SET status=? WHERE id=?";

    //     try {
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([$status, $taskId]);
    //         return $this->sendPayload(null, "success", "Task status updated successfully", $code);
    //     } catch (\PDOException $e) {
    //         $errmsg = $e->getMessage();
    //         $code = 400;
    //     }
    //     return $this->sendPayload(null, "failed", $errmsg, $code);
    // }

    // public function bagostatus($data, $id)
    // {
    //     $sql = "UPDATE todolist
    //                     SET 
    //                         status = ?
    //                     WHERE
    //                         id = ?;";
    //     try {
    //         $statement = $this->pdo->prepare($sql);
    //         $statement->execute(
    //             [
    //                 $data->status,
    //                 $id
    //             ]
    //         );
    //         return $this->sendPayload(null, "success", "Successfully updated task.", 200);
    //     } catch (\PDOException $e) {
    //         $errmsg = $e->getMessage();
    //         $code = 400;
    //     }

    //     return $this->sendPayload(null, "failed", $errmsg, $code);
    // }

    // public function updateangOrder($data, $id)
    // {
    //     $sql = "UPDATE todolist SET `order` = :order WHERE id = :id";

    //     try {
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([
    //             $data->order,
    //             $id
    //         ]);
    //         return $this->sendPayload(null, "success", "Task status updated successfully", null);
    //     } catch (\PDOException $e) {
    //         $errmsg = $e->getMessage();
    //         $code = 400;
    //     }
    //     return $this->sendPayload(null, "failed", $errmsg, $code);
    // }

    public function executeQuery($sql)
    {
        $data = array(); //place to store records retrieved for db
        $errmsg = ""; //initialized error message variable
        $code = 0; //initialize status code variable

        try {
            if ($result = $this->pdo->query($sql)->fetchAll()) { //retrieved records from db, returns false if no records found
                foreach ($result as $record) {
                    array_push($data, $record);
                }
                $code = 200;
                $result = null;
                return array("code" => $code, "data" => $data);
            } else {
                //if no record found, assign corresponding values to error messages/status
                $errmsg = "No records found";
                $code = 404;
            }
        } catch (\PDOException $e) {
            //PDO errors, mysql errors
            $errmsg = $e->getMessage();
            $code = 403;
        }
        return array("code" => $code, "errmsg" => $errmsg);
    }


    public function addEvent($data, $file = null) {
        try {
            $uploadDir = "../uploads/events/";
            $imagePath = '';
            
            // Check if image was uploaded
            if (isset($file['image']) && $file['image']['error'] === 0) {
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = uniqid() . '_' . basename($file['image']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['image']['tmp_name'], $targetPath)) {
                    $imagePath = $fileName;
                }
            }

            $sql = "INSERT INTO events (title, description, date, time, location, attendees, status, image) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['date'],
                $data['time'],
                $data['location'],
                $data['attendees'],
                $data['status'],
                $imagePath
            ]);

            // Get the base URL for images
            $baseUrl = "http://localhost/demo2/demoproject/api/uploads/events/";
            $imageUrl = $imagePath ? $baseUrl . $imagePath : '';

            return [
                "status" => "success",
                "message" => "Successfully added new event.",
                "data" => [
                    "image" => $imageUrl
                ]
            ];
        } catch(PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to add event: " . $e->getMessage()
            ];
        }
    }

    public function updateEventStatus($data) {
        try {
            $sql = "UPDATE events SET status = ? WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['status'],
                $data['id']
            ]);

            return [
                "status" => "success",
                "message" => "Successfully updated event status.",
                "data" => null
            ];
        } catch(PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to update event status: " . $e->getMessage()
            ];
        }
    }
    
    


    

    

    public function delete_events($id)
    {
        if (empty($id)) {
            return $this->sendPayload(null, "error", "ID is missing", 400);
        }

        $stmt = $this->pdo->prepare("DELETE FROM events WHERE id = ?");

        try {
            $stmt->execute([$id]);
            return $this->sendPayload(null, "success", "Events deleted successfully", 200);
        } catch (\PDOException $e) {
            return $this->sendPayload(null, "error", $e->getMessage(), 400);
        }
    }


    // public function submitInquiry($data)
    // {
    //     try {
    //         if (
    //             !isset($data->user_id) ||
    //             !isset($data->name) || !isset($data->email) ||
    //             !isset($data->phone) || !isset($data->message)
    //         ) {
    //             http_response_code(400);
    //             return json_encode(["message" => "All fields are required"]);
    //         }

    //         $sql = "INSERT INTO inquiries (user_id, name, email, phone, message) 
    //                 VALUES (?, ?, ?, ?, ?)";

    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([
    //             $data->user_id,
    //             $data->name,
    //             $data->email,
    //             $data->phone,
    //             $data->message
    //         ]);

    //         return json_encode(["message" => "Inquiry submitted successfully"]);
    //     } catch (\PDOException $e) {
    //         http_response_code(500);
    //         return json_encode(["message" => "Failed to submit inquiry: " . $e->getMessage()]);
    //     }
    // }

    public function delete_inquiry($id)
    {
        if (empty($id)) {
            return $this->sendPayload(null, "error", "ID is missing", 400);
        }

        $stmt = $this->pdo->prepare("DELETE FROM inquiries WHERE id = ?");

        try {
            $stmt->execute([$id]);
            return $this->sendPayload(null, "success", "Inquiry deleted successfully", 200);
        } catch (\PDOException $e) {
            return $this->sendPayload(null, "error", $e->getMessage(), 400);
        }
    }

    // public function submitCustomerCare($data)
    // {
    //     try {
    //         // Validate required fields
    //         if (
    //             !isset($data->name) || !isset($data->email) ||
    //             !isset($data->phone) || !isset($data->concern_type) ||
    //             !isset($data->description)
    //         ) {
    //             http_response_code(400);
    //             return json_encode(["message" => "All fields are required"]);
    //         }

    //         $sql = "INSERT INTO customer_care (name, email, phone, concern_type, description) 
    //             VALUES (?, ?, ?, ?, ?)";

    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([
    //             $data->name,
    //             $data->email,
    //             $data->phone,
    //             $data->concern_type,
    //             $data->description
    //         ]);

    //         return json_encode(["message" => "Customer care request submitted successfully"]);
    //     } catch (\PDOException $e) {
    //         http_response_code(500);
    //         return json_encode(["message" => "Failed to submit customer care request: " . $e->getMessage()]);
    //     }
    // }

    public function submitCustomerCare()
{
    try {
        // Create a data object from POST data
        $data = (object)[
            "user_id" => $_POST['user_id'] ?? null,
            "name" => $_POST['name'] ?? null,
            "email" => $_POST['email'] ?? null,
            "phone" => $_POST['phone'] ?? null,
            "concern_type" => $_POST['concern_type'] ?? null,
            "description" => $_POST['description'] ?? null,
        ];

        // Validate that the required fields are present
        if (!$data->user_id) {
            throw new Exception("Missing required fields: user_id.");
        }
        if (!$data->name) {
            throw new Exception("Missing required fields: name.");
        }
        if (!$data->email) {
            throw new Exception("Missing required fields: email.");
        }
        if (!$data->phone) {
            throw new Exception("Missing required fields: phone.");
        }
        if (!$data->concern_type) {
            throw new Exception("Missing required fields: concern_type.");
        }
        if (!$data->description) {
            throw new Exception("Missing required fields: description.");
        }

        // Prepare SQL statement
        $stmt = $this->pdo->prepare("INSERT INTO customer_care (user_id, name, email, phone, concern_type, description) VALUES (?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bindParam(1, $data->user_id);
        $stmt->bindParam(2, $data->name);
        $stmt->bindParam(3, $data->email);
        $stmt->bindParam(4, $data->phone);
        $stmt->bindParam(5, $data->concern_type);
        $stmt->bindParam(6, $data->description);

        // Execute the statement
        if ($stmt->execute()) {
            return json_encode(["message" => "Customer care request submitted successfully."]);
        } else {
            throw new Exception("Failed to submit customer care request.");
        }
    } catch (Exception $e) {
        // Catch any exceptions and return the error message
        return json_encode(["message" => $e->getMessage()]);
    }
}

    public function delete_customerCare($id)
    {
        if (empty($id)) {
            return $this->sendPayload(null, "error", "ID is missing", 400);
        }

        $stmt = $this->pdo->prepare("DELETE FROM customer_care WHERE id = ?");

        try {
            $stmt->execute([$id]);
            return $this->sendPayload(null, "success", "Customer care deleted successfully", 200);
        } catch (\PDOException $e) {
            return $this->sendPayload(null, "error", $e->getMessage(), 400);
        }
    }

    // public function submitTurnOver($data)
    // {
    //     try {
    //         // Validate required fields
    //         if (
    //             !isset($data->name) || !isset($data->email) ||
    //             !isset($data->phone) || !isset($data->unit_number) ||
    //             !isset($data->date)
    //         ) {
    //             http_response_code(400);
    //             return json_encode(["message" => "All fields are required"]);
    //         }

    //         $sql = "INSERT INTO unit_turnover (name, email, phone, unit_number, date, notes) 
    //             VALUES (?, ?, ?, ?, ?, ?)";

    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([
    //             $data->name,
    //             $data->email,
    //             $data->phone,
    //             $data->unit_number,
    //             $data->date,
    //             $data->notes ?? '' // Notes are optional
    //         ]);

    //         return json_encode(["message" => "Turn over request submitted successfully"]);
    //     } catch (\PDOException $e) {
    //         http_response_code(500);
    //         return json_encode(["message" => "Failed to submit turn over request: " . $e->getMessage()]);
    //     }
    // }
    public function submitTurnOver()
    {
        try {
            // Create a data object from POST data
            $data = (object)[
                "user_id" => $_POST['user_id'] ?? null,
                "name" => $_POST['name'] ?? null,
                "email" => $_POST['email'] ?? null,
                "phone" => $_POST['phone'] ?? null,
                "unit_number" => $_POST['unit_number'] ?? null,
                "date" => $_POST['date'] ?? null,
                "notes" => $_POST['notes'] ?? '', // Notes are optional
            ];
    
            // Validate that the required fields are present
            if (!$data->user_id) {
                throw new Exception("Missing required fields: user_id.");
            }
            if (!$data->name) {
                throw new Exception("Missing required fields: name.");
            }
            if (!$data->email) {
                throw new Exception("Missing required fields: email.");
            }
            if (!$data->phone) {
                throw new Exception("Missing required fields: phone.");
            }
            if (!$data->unit_number) {
                throw new Exception("Missing required fields: unit_number.");
            }
            if (!$data->date) {
                throw new Exception("Missing required fields: date.");
            }
    
            // Prepare SQL statement
            $stmt = $this->pdo->prepare("INSERT INTO unit_turnover (user_id, name, email, phone, unit_number, date, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
            // Bind parameters
            $stmt->bindParam(1, $data->user_id);
            $stmt->bindParam(2, $data->name);
            $stmt->bindParam(3, $data->email);
            $stmt->bindParam(4, $data->phone);
            $stmt->bindParam(5, $data->unit_number);
            $stmt->bindParam(6, $data->date);
            $stmt->bindParam(7, $data->notes);
    
            // Execute the statement
            if ($stmt->execute()) {
                return json_encode(["message" => "Turnover submitted successfully."]);
            } else {
                throw new Exception("Failed to submit turnover.");
            }
        } catch (Exception $e) {
            // Catch any exceptions and return the error message
            return json_encode(["message" => $e->getMessage()]);
        }
    }
    
    public function delete_turnover($id)
    {
        if (empty($id)) {
            return $this->sendPayload(null, "error", "ID is missing", 400);
        }

        $stmt = $this->pdo->prepare("DELETE FROM unit_turnover WHERE id = ?");

        try {
            $stmt->execute([$id]);
            return $this->sendPayload(null, "success", "Turnover deleted successfully", 200);
        } catch (\PDOException $e) {
            return $this->sendPayload(null, "error", $e->getMessage(), 400);
        }
    }


    // public function submitTrippingRequest($data)
    // {
    //     try {
    //         // Validate required fields
    //         if (
    //             !isset($data->name) || !isset($data->email) ||
    //             !isset($data->phone) || !isset($data->date) ||
    //             !isset($data->time) || !isset($data->property_of_interest)
    //         ) {
    //             http_response_code(400);
    //             return json_encode(["message" => "All fields are required"]);
    //         }

    //         // Prepare SQL statement
    //         $sql = "INSERT INTO tripping_request (name, email, phone, date, time, property_of_interest) 
    //                     VALUES (?, ?, ?, ?, ?, ?)";

    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([
    //             $data->name,
    //             $data->email,
    //             $data->phone,
    //             $data->date,
    //             $data->time,
    //             $data->property_of_interest
    //         ]);

    //         return json_encode(["message" => "Tripping request submitted successfully"]);
    //     } catch (\PDOException $e) {
    //         http_response_code(500);
    //         return json_encode(["message" => "Failed to submit tripping request: " . $e->getMessage()]);
    //     }
    // }

    public function submitTrippingRequest()
{
    try {
        // Create a data object from POST data
        $data = (object)[
            "user_id" => $_POST['user_id'] ?? null,
            "name" => $_POST['name'] ?? null,
            "email" => $_POST['email'] ?? null,
            "phone" => $_POST['phone'] ?? null,
            "date" => $_POST['date'] ?? null,
            "time" => $_POST['time'] ?? null,
            "property_of_interest" => $_POST['property_of_interest'] ?? null
        ];

        // Validate that the required fields are present
        if (!$data->user_id) {
            throw new Exception("Missing required fields: user_id.");
        }
        if (!$data->name) {
            throw new Exception("Missing required fields: name.");
        }
        if (!$data->email) {
            throw new Exception("Missing required fields: email.");
        }
        if (!$data->phone) {
            throw new Exception("Missing required fields: phone.");
        }
        if (!$data->date) {
            throw new Exception("Missing required fields: date.");
        }
        if (!$data->time) {
            throw new Exception("Missing required fields: time.");
        }
        if (!$data->property_of_interest) {
            throw new Exception("Missing required fields: property of interest.");
        }

        // Prepare SQL statement
        $stmt = $this->pdo->prepare("INSERT INTO tripping_request (user_id, name, email, phone, date, time, property_of_interest) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bindParam(1, $data->user_id);
        $stmt->bindParam(2, $data->name);
        $stmt->bindParam(3, $data->email);
        $stmt->bindParam(4, $data->phone);
        $stmt->bindParam(5, $data->date);
        $stmt->bindParam(6, $data->time);
        $stmt->bindParam(7, $data->property_of_interest);

        // Execute the statement
        if ($stmt->execute()) {
            return json_encode(["message" => "Tripping submitted successfully."]);
        } else {
            throw new Exception("Failed to submit tripping.");
        }
    } catch (Exception $e) {
        // Catch any exceptions and return the error message
        return json_encode(["message" => $e->getMessage()]);
    }
}

    public function delete_tripping($id)
    {
        if (empty($id)) {
            return $this->sendPayload(null, "error", "ID is missing", 400);
        }

        $stmt = $this->pdo->prepare("DELETE FROM tripping_request WHERE id = ?");

        try {
            $stmt->execute([$id]);
            return $this->sendPayload(null, "success", "Tripping deleted successfully", 200);
        } catch (\PDOException $e) {
            return $this->sendPayload(null, "error", $e->getMessage(), 400);
        }
    }

    public function adminSignup($data)
    {
        if (isset($data->username) && isset($data->email) && isset($data->password) && isset($data->adminCode)) {
            $username = $data->username;
            $email = $data->email;
            $password = password_hash($data->password, PASSWORD_BCRYPT);
            $adminCode = $data->adminCode;

            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(409);
                return json_encode(["message" => "Email already exists"]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO admins (username, email, password, admin_code) VALUES (:username, :email, :password, :adminCode)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':adminCode', $adminCode);

                if ($stmt->execute()) {
                    return json_encode(["message" => "Admin signup successful"]);
                } else {
                    http_response_code(500);
                    return json_encode(["message" => "Internal server error"]);
                }
            }
        } else {
            http_response_code(400);
            return json_encode(["message" => "All fields are required"]);
        }
    }

    public function adminLogin($data)
    {
        $secret_key = "63448c0f19663276ceabdc626d7aab8855872cc7ef5b152d099c41dcbbccd4ce";
        $issuer_claim = "http://localhost";
        $audience_claim = "http://localhost";
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10; // Token not valid before 10 seconds
        $expire_claim = $issuedat_claim + 3600; // Token expires in 1 hour

        if (isset($data->email) && isset($data->password) && isset($data->adminCode)) {
            $email = $data->email;
            $password = $data->password;
            $adminCode = $data->adminCode;

            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = :email AND admin_code = :adminCode");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':adminCode', $adminCode);
            $stmt->execute();

            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => $admin['admin_id'],
                        "email" => $admin['email'],
                        "role" => "admin"
                    )
                );

                $jwt = JWT::encode($token, $secret_key, 'HS256');

                return json_encode(array(
                    "message" => "Admin login successful",
                    "jwt" => $jwt,
                    "email" => $email,
                    "expireAt" => $expire_claim
                ));
            } else {
                http_response_code(401);
                return json_encode(array("message" => "Invalid email, password, or admin code"));
            }
        } else {
            http_response_code(400);
            return json_encode(array("message" => "Email, password, and admin code are required"));
        }
    }

    // public function delete_property($id)
    // {
    //     try {
    //         $sql = "DELETE FROM properties WHERE prop_id = ?";
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([$id]);

    //         return json_encode(["message" => "Property deleted successfully"]);
    //     } catch (\PDOException $e) {
    //         return json_encode(["error" => $e->getMessage()]);
    //     }
    // }

    // public function update_property($data, $id) {
    //     try {
    //         $sql = "UPDATE properties SET 
    //                 prop_name = :prop_name,
    //                 prop_address = :prop_address,
    //                 prop_size = :prop_size,
    //                 prop_rooms = :prop_rooms,
    //                 prop_status = :prop_status,
    //                 prop_price = :prop_price
    //                 WHERE prop_id = :prop_id";

    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([
    //             ':prop_name' => $data->prop_name,
    //             ':prop_address' => $data->prop_address,
    //             ':prop_size' => $data->prop_size,
    //             ':prop_rooms' => $data->prop_rooms,
    //             ':prop_status' => $data->prop_status,
    //             ':prop_price' => $data->prop_price,
    //             ':prop_id' => $id
    //         ]);

    //         if ($stmt->rowCount() > 0) {
    //             return json_encode(["status" => "success", "message" => "Property updated successfully"]);
    //         } else {
    //             return json_encode(["status" => "error", "message" => "No changes made or property not found"]);
    //         }
    //     } catch (\PDOException $e) {
    //         return json_encode(["status" => "error", "message" => $e->getMessage()]);
    //     }
    // }

    // public function delete_property($id) {
    //     try {
    //         $sql = "DELETE FROM properties WHERE prop_id = ?";
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute([$id]);

    //         if ($stmt->rowCount() > 0) {
    //             return json_encode(["status" => "success", "message" => "Property deleted successfully"]);
    //         } else {
    //             return json_encode(["status" => "error", "message" => "Property not found"]);
    //         }
    //     } catch (\PDOException $e) {
    //         return json_encode(["status" => "error", "message" => $e->getMessage()]);
    //     }
    // }

    public function updateApplicationStatus($data)
    {
        try {
            // Validate only the required fields for status update
            if (!isset($data->status)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Status is required"
                );
            }

            // Validate status values
            $allowedStatuses = ['Approved', 'Rejected', 'Pending'];
            if (!in_array($data->status, $allowedStatuses)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Invalid status value"
                );
            }

            $sql = "UPDATE user_applications SET status = :status WHERE appl_id = :appl_id";
            $stmt = $this->pdo->prepare($sql);

            $params = array(
                ':status' => $data->status,
                ':appl_id' => $data->appl_id
            );

            if ($stmt->execute($params)) {
                return array(
                    "code" => 200,
                    "status" => "success",
                    "message" => "Application status updated successfully",
                    "data" => array(
                        "appl_id" => $data->appl_id,
                        "new_status" => $data->status
                    )
                );
            } else {
                return array(
                    "code" => 500,
                    "status" => "error",
                    "message" => "Failed to update application status"
                );
            }
        } catch (\PDOException $e) {
            return array(
                "code" => 500,
                "status" => "error",
                "message" => $e->getMessage()
            );
        }
    }

    public function updateCustomerCareStatus($data)
    {
        try {
            // Validate only the required fields for status update
            if (!isset($data->status)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Status is required"
                );
            }

            // Validate status values
            $allowedStatuses = ['Approved', 'Rejected', 'Pending'];
            if (!in_array($data->status, $allowedStatuses)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Invalid status value"
                );
            }

            $sql = "UPDATE customer_care SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            $params = array(
                ':status' => $data->status,
                ':id' => $data->id
            );

            if ($stmt->execute($params)) {
                return array(
                    "code" => 200,
                    "status" => "success",
                    "message" => "Application status updated successfully",
                    "data" => array(
                        "appl_id" => $data->id,
                        "new_status" => $data->status
                    )
                );
            } else {
                return array(
                    "code" => 500,
                    "status" => "error",
                    "message" => "Failed to update application status"
                );
            }
        } catch (\PDOException $e) {
            return array(
                "code" => 500,
                "status" => "error",
                "message" => $e->getMessage()
            );
        }
    }

    public function updateTrippingStatus($data)
    {
        try {
            // Validate only the required fields for status update
            if (!isset($data->status)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Status is required"
                );
            }

            // Validate status values
            $allowedStatuses = ['Approved', 'Rejected', 'Pending'];
            if (!in_array($data->status, $allowedStatuses)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Invalid status value"
                );
            }

            $sql = "UPDATE tripping_request SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            $params = array(
                ':status' => $data->status,
                ':id' => $data->id
            );

            if ($stmt->execute($params)) {
                return array(
                    "code" => 200,
                    "status" => "success",
                    "message" => "Application status updated successfully",
                    "data" => array(
                        "appl_id" => $data->id,
                        "new_status" => $data->status
                    )
                );
            } else {
                return array(
                    "code" => 500,
                    "status" => "error",
                    "message" => "Failed to update application status"
                );
            }
        } catch (\PDOException $e) {
            return array(
                "code" => 500,
                "status" => "error",
                "message" => $e->getMessage()
            );
        }
    }

    public function updateTurnOverStatus($data)
    {
        try {
            // Validate only the required fields for status update
            if (!isset($data->status)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Status is required"
                );
            }

            // Validate status values
            $allowedStatuses = ['Approved', 'Rejected', 'Pending'];
            if (!in_array($data->status, $allowedStatuses)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Invalid status value"
                );
            }

            $sql = "UPDATE unit_turnover SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            $params = array(
                ':status' => $data->status,
                ':id' => $data->id
            );

            if ($stmt->execute($params)) {
                return array(
                    "code" => 200,
                    "status" => "success",
                    "message" => "Application status updated successfully",
                    "data" => array(
                        "appl_id" => $data->id,
                        "new_status" => $data->status
                    )
                );
            } else {
                return array(
                    "code" => 500,
                    "status" => "error",
                    "message" => "Failed to update application status"
                );
            }
        } catch (\PDOException $e) {
            return array(
                "code" => 500,
                "status" => "error",
                "message" => $e->getMessage()
            );
        }
    }

    public function updateInquiriesStatus($data)
    {
        try {
            // Validate only the required fields for status update
            if (!isset($data->status)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Status is required"
                );
            }

            // Validate status values
            $allowedStatuses = ['Approved', 'Rejected', 'Pending'];
            if (!in_array($data->status, $allowedStatuses)) {
                return array(
                    "code" => 400,
                    "status" => "error",
                    "message" => "Invalid status value"
                );
            }

            $sql = "UPDATE inquiries SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            $params = array(
                ':status' => $data->status,
                ':id' => $data->id
            );

            if ($stmt->execute($params)) {
                return array(
                    "code" => 200,
                    "status" => "success",
                    "message" => "Inquiries status updated successfully",
                    "data" => array(
                        "appl_id" => $data->id,
                        "new_status" => $data->status
                    )
                );
            } else {
                return array(
                    "code" => 500,
                    "status" => "error",
                    "message" => "Failed to update inquiries status"
                );
            }
        } catch (\PDOException $e) {
            return array(
                "code" => 500,
                "status" => "error",
                "message" => $e->getMessage()
            );
        }
    }

    public function transferUserEmails($data)
    {
        try {
            // Validate only the required fields for status update
            if (!isset($data->email_transfer)) {
                return array(
                    "code" => 400,
                    "email_transfer" => "error",
                    "message" => "email_transfer is required"
                );
            }

            // Validate status values
            $allowedStatuses = ['Not Sent', 'Sent'];
            if (!in_array($data->email_transfer, $allowedStatuses)) {
                return array(
                    "code" => 400,
                    "email_transfer" => "error",
                    "message" => "Invalid email transfer value"
                );
            }

            $sql = "UPDATE user_applications SET email_transfer = :email_transfer WHERE appl_id = :appl_id";
            $stmt = $this->pdo->prepare($sql);

            $params = array(
                ':email_transfer' => $data->email_transfer,
                ':appl_id' => $data->appl_id
            );

            if ($stmt->execute($params)) {
                return array(
                    "code" => 200,
                    "email_transfer" => "success",
                    "message" => "Sent email successfully",
                    "data" => array(
                        "appl_id" => $data->appl_id,
                        "new_email_transfer" => $data->email_transfer
                    )
                );
            } else {
                return array(
                    "code" => 500,
                    "email_transfer" => "error",
                    "message" => "Failed to sent email"
                );
            }
        } catch (\PDOException $e) {
            return array(
                "code" => 500,
                "email_transfer" => "error",
                "message" => $e->getMessage()
            );
        }
    }

    public function addUser($data)
{
    if (isset($data->email) && isset($data->username) && isset($data->password)) {
        $email = htmlspecialchars(strip_tags($data->email));
        $username = htmlspecialchars(strip_tags($data->username));
        $password = $data->password; // Use the fetched password directly

        // Check if the user already exists
        $stmt = $this->pdo->prepare("SELECT * FROM hoa_users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            http_response_code(409);
            return json_encode(["message" => "Email already exists"]);
        } else {
            // Insert the new user into the hoa_users table
            $stmt = $this->pdo->prepare("INSERT INTO hoa_users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password); // Use the fetched password

            if ($stmt->execute()) {
                return json_encode(["message" => "User added successfully"]);
            } else {
                http_response_code(500);
                return json_encode(["message" => "Internal server error"]);
            }
        }
    } else {
        http_response_code(400);
        return json_encode(["message" => "Email, username, and password are required"]);
    }
}

    public function addClass($data) {
        try {
            // Validate required fields
            if (!isset($data->class_name) || !isset($data->description)) {
                return [
                    "status" => "error",
                    "message" => "Class name and description are required"
                ];
            }

            // Prepare the SQL statement
            $sql = "INSERT INTO class_routines (
                class_name,
                description,
                admin_id,
                mondayRoutine,
                tuesdayRoutine,
                wednesdayRoutine,
                thursdayRoutine,
                fridayRoutine,
                saturdayRoutine,
                sundayRoutine,
                mondayintensity,
                tuesdayintensity,
                wednesdayintensity,
                thursdayintensity,
                fridayintensity,
                saturdayintensity,
                sundayintensity
            ) VALUES (
                :class_name,
                :description,
                :admin_id,
                :mondayRoutine,
                :tuesdayRoutine,
                :wednesdayRoutine,
                :thursdayRoutine,
                :fridayRoutine,
                :saturdayRoutine,
                :sundayRoutine,
                :mondayintensity,
                :tuesdayintensity,
                :wednesdayintensity,
                :thursdayintensity,
                :fridayintensity,
                :saturdayintensity,
                :sundayintensity
            )";

            // Prepare and execute the statement
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':class_name' => $data->class_name,
                ':description' => $data->description,
                ':admin_id' => $data->admin_id ?? null,
                ':mondayRoutine' => $data->mondayRoutine ?? '',
                ':tuesdayRoutine' => $data->tuesdayRoutine ?? '',
                ':wednesdayRoutine' => $data->wednesdayRoutine ?? '',
                ':thursdayRoutine' => $data->thursdayRoutine ?? '',
                ':fridayRoutine' => $data->fridayRoutine ?? '',
                ':saturdayRoutine' => $data->saturdayRoutine ?? '',
                ':sundayRoutine' => $data->sundayRoutine ?? '',
                ':mondayintensity' => $data->mondayintensity ?? '',
                ':tuesdayintensity' => $data->tuesdayintensity ?? '',
                ':wednesdayintensity' => $data->wednesdayintensity ?? '',
                ':thursdayintensity' => $data->thursdayintensity ?? '',
                ':fridayintensity' => $data->fridayintensity ?? '',
                ':saturdayintensity' => $data->saturdayintensity ?? '',
                ':sundayintensity' => $data->sundayintensity ?? ''
            ]);

            return [
                "status" => "success",
                "message" => "Class added successfully",
                "class_id" => $this->pdo->lastInsertId()
            ];

        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to add class: " . $e->getMessage()
            ];
        }
    }

    public function editClass($data) {
        try {
            if (!isset($data->class_id)) {
                return [
                    "status" => "error",
                    "message" => "Class ID is required"
                ];
            }
            $sql = "UPDATE class_routines SET
                mondayRoutine = :mondayRoutine,
                tuesdayRoutine = :tuesdayRoutine,
                wednesdayRoutine = :wednesdayRoutine,
                thursdayRoutine = :thursdayRoutine,
                fridayRoutine = :fridayRoutine,
                saturdayRoutine = :saturdayRoutine,
                sundayRoutine = :sundayRoutine,
                mondayintensity = :mondayintensity,
                tuesdayintensity = :tuesdayintensity,
                wednesdayintensity = :wednesdayintensity,
                thursdayintensity = :thursdayintensity,
                fridayintensity = :fridayintensity,
                saturdayintensity = :saturdayintensity,
                sundayintensity = :sundayintensity
            WHERE class_id = :class_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':mondayRoutine' => $data->mondayRoutine ?? '',
                ':tuesdayRoutine' => $data->tuesdayRoutine ?? '',
                ':wednesdayRoutine' => $data->wednesdayRoutine ?? '',
                ':thursdayRoutine' => $data->thursdayRoutine ?? '',
                ':fridayRoutine' => $data->fridayRoutine ?? '',
                ':saturdayRoutine' => $data->saturdayRoutine ?? '',
                ':sundayRoutine' => $data->sundayRoutine ?? '',
                ':mondayintensity' => $data->mondayintensity ?? '',
                ':tuesdayintensity' => $data->tuesdayintensity ?? '',
                ':wednesdayintensity' => $data->wednesdayintensity ?? '',
                ':thursdayintensity' => $data->thursdayintensity ?? '',
                ':fridayintensity' => $data->fridayintensity ?? '',
                ':saturdayintensity' => $data->saturdayintensity ?? '',
                ':sundayintensity' => $data->sundayintensity ?? '',
                ':class_id' => $data->class_id
            ]);
            return [
                "status" => "success",
                "message" => "Class updated successfully"
            ];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to update class: " . $e->getMessage()
            ];
        }
    }

    public function generateTokens($data) {
        try {
            if (!isset($data->count) || !isset($data->class_id) || !isset($data->admin_id)) {
                return [
                    "status" => "error",
                    "message" => "Count, class_id, and admin_id are required"
                ];
            }

            $count = (int)$data->count;
            $class_id = $data->class_id;
            $admin_id = $data->admin_id;
            $generatedTokens = [];

            for ($i = 0; $i < $count; $i++) {
                $token = $this->generateUniqueToken();
                $generatedTokens[] = $token;

                // Insert the token into the database
                $sql = "INSERT INTO codegen (class_id, code, Requestedbycoach, time) VALUES (:class_id, :code, :admin_id, NOW())";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':class_id' => $class_id,
                    ':code' => $token,
                    ':admin_id' => $admin_id
                ]);
            }

            return [
                "status" => "success",
                "message" => "Tokens generated successfully",
                "tokens" => $generatedTokens,
                "count" => $count
            ];

        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Failed to generate tokens: " . $e->getMessage()
            ];
        }
    }

    private function generateUniqueToken() {
        $maxAttempts = 100; // Prevent infinite loops
        $attempts = 0;
        
        do {
            $token = $this->generateRandomToken();
            $attempts++;
            
            // Check if token already exists in database
            $sql = "SELECT COUNT(*) FROM codegen WHERE code = :code";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':code' => $token]);
            $exists = $stmt->fetchColumn() > 0;
            
            if (!$exists) {
                return $token;
            }
            
        } while ($attempts < $maxAttempts);
        
        // If we've tried too many times, generate a timestamp-based unique token
        return $this->generateTimestampToken();
    }

    private function generateRandomToken() {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $token = '';
        
        for ($i = 0; $i < 7; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $token;
    }

    private function generateTimestampToken() {
        $timestamp = time();
        $random = rand(100, 999);
        return 'T' . $timestamp . $random;
    }

    // Routines POST methods
    public function validateAndRedeemToken($data) {
        try {
            // Debug: Log the received data
            error_log("=== validateAndRedeemToken START ===");
            
            // Handle different data types safely
            if (is_array($data)) {
                error_log("Raw data received (array): " . print_r($data, true));
                error_log("Data keys: " . implode(', ', array_keys($data)));
            } elseif (is_object($data)) {
                error_log("Raw data received (object): " . print_r($data, true));
                error_log("Object properties: " . implode(', ', get_object_vars($data)));
            } else {
                error_log("Raw data received (other type): " . var_export($data, true));
            }
            
            error_log("Data type: " . gettype($data));
            
            // Safely extract parameters from array or object
            $token = null;
            $studentUsername = null;
            $studentUserId = null;
            $userData = null; // initialize for safe logging later
            
            if (is_array($data)) {
                $token = $data['token'] ?? null;
                $studentUsername = $data['student_username'] ?? null;
                $studentUserId = isset($data['user_id']) ? (int)$data['user_id'] : null;
            } elseif (is_object($data)) {
                $token = $data->token ?? null;
                $studentUsername = $data->student_username ?? null;
                $studentUserId = isset($data->user_id) ? (int)$data->user_id : null;
            }
            
            if (!$token || (!$studentUsername && !$studentUserId)) {
                error_log("Missing required parameters - token: " . ($token ? 'present' : 'missing') . ", student_username: " . ($studentUsername ? 'present' : 'missing') . ", user_id: " . ($studentUserId ? 'present' : 'missing'));
                return [
                    "status" => "error",
                    "message" => "Token and student identifier are required"
                ];
            }
            
            // Debug: Log the extracted parameters
            error_log("Extracted token: '$token' (type: " . gettype($token) . ")");
            error_log("Extracted student_username: '$studentUsername' (type: " . gettype($studentUsername) . ")");
            error_log("Extracted user_id: '" . ($studentUserId !== null ? $studentUserId : 'null') . "' (type: " . gettype($studentUserId) . ")");
            
            // Validate parameter types
            if (!is_string($token) || ($studentUsername !== null && !is_string($studentUsername))) {
                error_log("Invalid parameter types - token: " . gettype($token) . ", student_username: " . gettype($studentUsername));
                return [
                    "status" => "error",
                    "message" => "Invalid parameter types"
                ];
            }

            // Check if token exists
            $sql = "SELECT cg.code_id, cg.class_id, cg.Requestedbycoach, cg.student_redeemer, cg.user_id, cr.class_name as title, cg.Requestedbycoach as coach_username
                    FROM codegen cg
                    INNER JOIN class_routines cr ON cg.class_id = cr.class_id
                    WHERE cg.code = :code";
            
            error_log("SQL Query: " . $sql);
            error_log("Token being searched: '$token'");
            
            try {
                $stmt = $this->pdo->prepare($sql);
                if (!$stmt) {
                    error_log("Failed to prepare SQL statement");
                    return [
                        "status" => "error",
                        "message" => "Database error: failed to prepare statement"
                    ];
                }
                
                $stmt->bindParam(':code', $token, PDO::PARAM_STR);
                $stmt->execute();
                $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Database error in token query: " . $e->getMessage());
                return [
                    "status" => "error",
                    "message" => "Database error: " . $e->getMessage()
                ];
            }
            
            // Debug logging
            error_log("Token query executed successfully");
            error_log("Token query result: " . print_r($tokenData, true));
            error_log("Token data type: " . gettype($tokenData));
            error_log("Token data count: " . ($tokenData ? count($tokenData) : 'null'));
            
            // Check if the JOIN with class_routines was successful
            if (!$tokenData || !isset($tokenData['title'])) {
                error_log("Token found but class_routines JOIN failed or class_name is missing");
                error_log("This might indicate that the class_routines table doesn't exist or has different column names");
                return [
                    "status" => "error",
                    "message" => "Class information not found. Please contact administrator."
                ];
            }

            if (!$tokenData) {
                error_log("=== validateAndRedeemToken END - INVALID TOKEN ===");
                error_log("Token '$token' not found in database");
                return [
                    "status" => "error",
                    "message" => "Invalid token"
                ];
            }
            
            // Validate required fields
            if (!isset($tokenData['class_id']) || !isset($tokenData['title']) || !isset($tokenData['coach_username'])) {
                error_log("Token data missing required fields: " . print_r($tokenData, true));
                return [
                    "status" => "error",
                    "message" => "Token data is incomplete"
                ];
            }
            
            // Check if token has already been redeemed
            if (!empty($tokenData['student_redeemer'])) {
                error_log("Token already redeemed by: " . $tokenData['student_redeemer']);
                return [
                    "status" => "error",
                    "message" => "Token already been redeemed"
                ];
            }

            // Check if token is valid (exists and has a class_id)
            if (!$tokenData['class_id']) {
                return [
                    "status" => "error",
                    "message" => "Invalid token - no class associated"
                ];
            }

            // Resolve user id either directly or via username
            $userId = $studentUserId;
            if ($userId === null) {
                $userSql = "SELECT user_id FROM hoa_users WHERE username = :username";
                error_log("User SQL Query: " . $userSql);
                error_log("Username being searched: '$studentUsername'");
                try {
                    $userStmt = $this->pdo->prepare($userSql);
                    if (!$userStmt) {
                        error_log("Failed to prepare user SQL statement");
                        return [
                            "status" => "error",
                            "message" => "Database error: failed to prepare user statement"
                        ];
                    }
                    $userStmt->bindParam(':username', $studentUsername, PDO::PARAM_STR);
                    $userStmt->execute();
                    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
                    $userId = $userData['user_id'] ?? null;
                } catch (PDOException $e) {
                    error_log("Database error in user query: " . $e->getMessage());
                    return [
                        "status" => "error",
                        "message" => "Database error: " . $e->getMessage()
                    ];
                }
            }
            
            // Debug logging
            error_log("User query executed successfully");
            if ($userData !== null) {
                error_log("User lookup result for '$studentUsername': " . print_r($userData, true));
                error_log("User data type: " . gettype($userData));
                error_log("User data count: " . ($userData ? count($userData) : 'null'));
            } else {
                error_log("User lookup used provided user_id: " . $userId);
            }

            if (!$userId) {
                error_log("=== validateAndRedeemToken END - STUDENT NOT FOUND ===");
                error_log("Student identifier not found in hoa_users table");
                return [
                    "status" => "error",
                    "message" => "Student not found"
                ];
            }
            
            // Update the codegen table to mark token as redeemed
            try {
                $updateSql = "UPDATE codegen SET student_redeemer = :student_username, `time redeemed` = NOW(), user_id = :user_id WHERE code = :code AND (student_redeemer IS NULL OR student_redeemer = '')";
                error_log("Update SQL: " . $updateSql);
                
                $updateStmt = $this->pdo->prepare($updateSql);
                if (!$updateStmt) {
                    error_log("Failed to prepare update statement");
                    return [
                        "status" => "error",
                        "message" => "Database error: failed to prepare update statement"
                    ];
                }
                
                $updateStmt->bindParam(':student_username', $studentUsername, PDO::PARAM_STR);
                $updateStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $updateStmt->bindParam(':code', $token, PDO::PARAM_STR);
                $updateStmt->execute();
                
                error_log("Token redemption recorded successfully");
            } catch (PDOException $e) {
                error_log("Database error updating token redemption: " . $e->getMessage());
                return [
                    "status" => "error",
                    "message" => "Database error: " . $e->getMessage()
                ];
            }

            // For now, we'll just validate the token and return success
            // You can add enrollment logic later when you have the necessary tables/fields
            
            $response = [
                "status" => "success",
                "message" => "Successfully enrolled in class",
                "classTitle" => $tokenData['title'],
                "coachUsername" => $tokenData['coach_username']
            ];
            
            error_log("Preparing response: " . print_r($response, true));
            error_log("=== validateAndRedeemToken END - SUCCESS ===");
            
            return $response;

        } catch (PDOException $e) {
            error_log("=== validateAndRedeemToken END - PDO ERROR ===");
            error_log("PDO Error in validateAndRedeemToken: " . $e->getMessage());
            error_log("PDO Error trace: " . $e->getTraceAsString());
            return [
                "status" => "error",
                "message" => "Failed to validate token: " . $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("=== validateAndRedeemToken END - GENERAL ERROR ===");
            error_log("General Error in validateAndRedeemToken: " . $e->getMessage());
            error_log("General Error trace: " . $e->getTraceAsString());
            return [
                "status" => "error",
                "message" => "Failed to validate token: " . $e->getMessage()
            ];
        }
    }

    public function submitRoutineCompletion($postData, $files) {
        try {
            error_log("=== submitRoutineCompletion START ===");
            error_log("POST data received: " . print_r($postData, true));
            error_log("FILES data received: " . print_r($files, true));
            
            // Validate required data
            if (!isset($postData['routineId']) || !isset($postData['userId'])) {
                error_log("Missing required data: routineId or userId");
                return [
                    "status" => "error",
                    "message" => "Missing required data"
                ];
            }

            $routineId = $postData['routineId'];
            $userId = $postData['userId'];

            // Check if class_id exists in class_routines table
            $classCheckSql = "SELECT class_id FROM class_routines WHERE class_id = :class_id";
            $classCheckStmt = $this->pdo->prepare($classCheckSql);
            $classCheckStmt->bindParam(':class_id', $routineId, PDO::PARAM_INT);
            $classCheckStmt->execute();
            
            if (!$classCheckStmt->fetch()) {
                error_log("Class ID not found in class_routines: " . $routineId);
                return [
                    "status" => "error",
                    "message" => "Class not found"
                ];
            }

            // Handle file upload
            if (!isset($files['image']) || $files['image']['error'] !== UPLOAD_ERR_OK) {
                return [
                    "status" => "error",
                    "message" => "Image upload failed"
                ];
            }

            // Cloudinary configuration
            $cloudinaryConfig = $this->getCloudinaryConfig();

            // Test Cloudinary connection first
            $connectionTest = $this->testCloudinaryConnection($cloudinaryConfig);
            if (!$connectionTest['success']) {
                error_log("Cloudinary connection test failed: " . $connectionTest['message']);
                return [
                    "status" => "error",
                    "message" => "Cloudinary connection failed: " . $connectionTest['message']
                ];
            }
            error_log("Cloudinary connection test successful: " . $connectionTest['message']);

            // Upload to Cloudinary
            $cloudinaryUrl = $this->uploadToCloudinary($files['image'], $cloudinaryConfig);
            
            if (!$cloudinaryUrl) {
                return [
                    "status" => "error",
                    "message" => "Failed to upload image to Cloudinary"
                ];
            }

            // Check if routine already completed today
            $today = date('Y-m-d');
            $checkSql = "SELECT id FROM routine_history 
                        WHERE class_id = :class_id 
                        AND user_id = :user_id 
                        AND DATE(date_of_submission) = :today";
            
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([
                ':class_id' => $routineId,
                ':user_id' => $userId,
                ':today' => $today
            ]);

            if ($checkStmt->fetch()) {
                return [
                    "status" => "error",
                    "message" => "Routine already completed today"
                ];
            }

            // Use Philippine timezone (UTC+8) for submission time and date
            $philippineTime = new DateTime('now', new DateTimeZone('UTC'));
            $philippineTime->modify('+8 hours');
            $philippineDate = $philippineTime->format('Y-m-d');
            $philippineTimeStr = $philippineTime->format('Y-m-d H:i:s');
            
            $insertSql = "INSERT INTO routine_history (class_id, user_id, routine, routine_intensity, time_of_submission, date_of_submission, img) 
                          VALUES (:class_id, :user_id, :routine, :intensity, :philippine_time, :philippine_date, :image_path)";
            
            $insertStmt = $this->pdo->prepare($insertSql);
            $result = $insertStmt->execute([
                ':class_id' => $routineId,
                ':user_id' => $userId,
                ':routine' => $postData['routine'] ?? 'Weekly Routine',
                ':intensity' => $postData['intensity'] ?? 'Low',
                ':philippine_time' => $philippineTimeStr,
                ':philippine_date' => $philippineDate,
                ':image_path' => $cloudinaryUrl
            ]);

            if (!$result) {
                error_log("Database insert failed: " . print_r($insertStmt->errorInfo(), true));
                return [
                    "status" => "error",
                    "message" => "Database insert failed"
                ];
            }

            return [
                "status" => "success",
                "message" => "Routine completion submitted successfully",
                "image_url" => $cloudinaryUrl
            ];
        } catch (Exception $e) {
            error_log("submitRoutineCompletion Exception: " . $e->getMessage());
            return [
                "status" => "error",
                "message" => "Exception: " . $e->getMessage()
            ];
        }
    }

    // Helper method to upload image to Cloudinary
    private function uploadToCloudinary($file, $config) {
        try {
            error_log("Starting Cloudinary upload with config: " . print_r($config, true));
            error_log("File info: " . print_r($file, true));
            
            // Validate file
            if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
                error_log("File validation failed: tmp_name not found or file doesn't exist");
                return false;
            }
            
            // Check file size
            $fileSize = filesize($file['tmp_name']);
            error_log("File size: " . $fileSize . " bytes");
            
            if ($fileSize === 0) {
                error_log("File is empty");
                return false;
            }
            
            // Create unique filename
            $fileName = 'routine_' . time() . '_' . uniqid();
            error_log("Generated filename: " . $fileName);
            
            // Cloudinary upload URL for signed uploads
            $uploadUrl = 'https://api.cloudinary.com/v1_1/' . $config['cloud_name'] . '/image/upload';
            error_log("Upload URL: " . $uploadUrl);
            
            // Create cURL request with file upload (not base64)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
            // Use multipart form data for file upload
            $postFields = [
                'file' => new CURLFile($file['tmp_name'], $file['type'], $file['name']),
                'public_id' => $config['folder'] . '/' . $fileName,
                'folder' => $config['folder']
            ];
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . base64_encode($config['api_key'] . ':' . $config['api_secret'])
            ]);
            
            error_log("cURL options set, executing request...");
            error_log("Post fields: " . print_r($postFields, true));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $curlInfo = curl_getinfo($ch);
            
            error_log("cURL response received - HTTP Code: " . $httpCode);
            error_log("cURL error (if any): " . $curlError);
            error_log("Response body: " . $response);
            
            curl_close($ch);
            
            if ($curlError) {
                error_log("cURL error occurred: " . $curlError);
                return false;
            }
            
            if ($httpCode === 200) {
                $result = json_decode($response, true);
                if (isset($result['secure_url'])) {
                    error_log("Cloudinary upload successful: " . $result['secure_url']);
                    return $result['secure_url'];
                } else {
                    error_log("Upload response missing secure_url: " . print_r($result, true));
                    return false;
                }
            } else {
                error_log("Cloudinary upload failed. HTTP Code: " . $httpCode . ", Response: " . $response);
                
                // Try to parse error response
                $errorResult = json_decode($response, true);
                if ($errorResult && isset($errorResult['error'])) {
                    error_log("Cloudinary error details: " . print_r($errorResult['error'], true));
                }
                
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Cloudinary upload exception: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            return false;
        }
    }

    // Check if Cloudinary is properly configured
    private function isCloudinaryConfigured(): bool {
        return getenv('CLOUDINARY_CLOUD_NAME') && getenv('CLOUDINARY_API_KEY') && getenv('CLOUDINARY_API_SECRET');
    }

    // Build Cloudinary URL for existing images or fallback to local path
    private function buildRoutineImageUrl(string $value): string {
        $val = trim($value);
        if ($val === '') return $val;
        
        // If it's already a full URL, return as is
        if (preg_match('/^https?:\/\//i', $val)) { 
            return $val; 
        }
        
        // If Cloudinary is configured, build Cloudinary URL
        if ($this->isCloudinaryConfigured()) {
            $cloud = getenv('CLOUDINARY_CLOUD_NAME');
            $folderRoot = getenv('CLOUDINARY_FOLDER') ?: 'athletrack';
            $file = basename($val);
            return "https://res.cloudinary.com/{$cloud}/image/upload/" . rtrim($folderRoot, '/') . "/routines/{$file}";
        }
        
        // Fallback to local path if Cloudinary not configured
        if (strpos($val, 'uploads/routines/') !== false) { 
            return 'uploads/routines/' . basename($val); 
        }
        if (strpos($val, 'routines/') === 0) { 
            return $val; 
        }
        return 'uploads/routines/' . basename($val);
    }

    // Get Cloudinary configuration from environment variables or use defaults
    private function getCloudinaryConfig(): array {
        // Try to get from CLOUDINARY_URL first (recommended format)
        $cloudinaryUrl = getenv('CLOUDINARY_URL');
        if ($cloudinaryUrl) {
            // Parse the CLOUDINARY_URL to extract credentials
            if (preg_match('/cloudinary:\/\/([^:]+):([^@]+)@([^\/]+)/', $cloudinaryUrl, $matches)) {
                return [
                    'cloud_name' => $matches[3],
                    'api_key' => $matches[1],
                    'api_secret' => $matches[2],
                    'folder' => getenv('CLOUDINARY_FOLDER') ?: 'athletrack'
                ];
            }
        }
        
        // Fallback to individual environment variables
        if ($this->isCloudinaryConfigured()) {
            return [
                'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME'),
                'api_key' => getenv('CLOUDINARY_API_KEY'),
                'api_secret' => getenv('CLOUDINARY_API_SECRET'),
                'folder' => getenv('CLOUDINARY_FOLDER') ?: 'athletrack'
            ];
        }
        
        // Final fallback to hardcoded values
        return [
            'cloud_name' => 'dtljwbojw',
            'api_key' => '179567916365545',
            'api_secret' => 'Ecug-lmZQyU_W03shr4O1PRXSAY',
            'folder' => 'athletrack'
        ];
    }

    // Test Cloudinary connection and verify account credentials
    private function testCloudinaryConnection($config) {
        try {
            error_log("Testing Cloudinary connection with config: " . print_r($config, true));
            
            // Test 1: Basic connectivity to Cloudinary API
            $testUrl = 'https://api.cloudinary.com/v1_1/' . $config['cloud_name'] . '/resources/image';
            error_log("Testing URL: " . $testUrl);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . base64_encode($config['api_key'] . ':' . $config['api_secret'])
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            error_log("Cloudinary test response - HTTP Code: " . $httpCode . ", Response: " . $response);
            
            if ($curlError) {
                error_log("cURL error: " . $curlError);
                return ['success' => false, 'message' => 'Network error: ' . $curlError];
            }
            
            if ($httpCode === 200) {
                $result = json_decode($response, true);
                if ($result && isset($result['resources'])) {
                    error_log("Successfully connected to Cloudinary account: " . $config['cloud_name']);
                    return [
                        'success' => true, 
                        'message' => 'Successfully connected to Cloudinary account: ' . $config['cloud_name'],
                        'account_info' => [
                            'cloud_name' => $config['cloud_name'],
                            'resource_count' => count($result['resources']),
                            'api_key' => substr($config['api_key'], 0, 8) . '...' // Partial key for security
                        ]
                    ];
                } else {
                    error_log("Unexpected response format from Cloudinary");
                    return ['success' => false, 'message' => 'Unexpected response format from Cloudinary'];
                }
            } elseif ($httpCode === 401) {
                error_log("Authentication failed - check API key and secret");
                return ['success' => false, 'message' => 'Authentication failed - check API key and secret'];
            } elseif ($httpCode === 404) {
                error_log("Cloud name not found: " . $config['cloud_name']);
                return ['success' => false, 'message' => 'Cloud name not found: ' . $config['cloud_name']];
            } else {
                error_log("Cloudinary API returned HTTP code: " . $httpCode);
                return ['success' => false, 'message' => 'Cloudinary API returned HTTP code: ' . $httpCode];
            }
            
        } catch (Exception $e) {
            error_log("Cloudinary connection test exception: " . $e->getMessage());
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    // Public endpoint to test Cloudinary connection
    public function testCloudinaryConnectionEndpoint() {
        try {
            $config = $this->getCloudinaryConfig();
            $result = $this->testCloudinaryConnection($config);
            
            return [
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
                'config_info' => [
                    'cloud_name' => $config['cloud_name'],
                    'api_key' => substr($config['api_key'], 0, 8) . '...',
                    'folder' => $config['folder']
                ],
                'details' => $result
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Test endpoint exception: ' . $e->getMessage()
            ];
        }
    }
}

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    error_log("Handling OPTIONS request");
    http_response_code(200);
    exit();
}



/**
 * Add a new employee with the provided data.
 *
 * @param array|object $data
 *   The data representing the new employee.
 *
 * @return array|object
 *   The added employee data.
 */
/**
 * Add a new job with the provided data.
 *
 * @param array|object $data
 *   The data representing the new job.
 *
 * @return array|object
 *   The added job data.
 */
