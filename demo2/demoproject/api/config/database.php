<?php

//set default time zone

date_default_timezone_set("Asia/Manila");

//set time limit of requests
set_time_limit(1000);

// Use environment variables for database configuration
$host = getenv('MYSQLHOST') ?: 'crossover.proxy.rlwy.net';
$port = getenv('MYSQLPORT') ?: '3306';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'gLjXtuyGRfwgmafkdLUeIvdOqVBspSnI';

error_log("Database config - Host: $host, Port: $port, DB: $db, User: $user");

//define constants for server credentials/configuration
define("SERVER", $host);
define("DATABASE", $db);
define("USER", $user);
define("PASSWORD", $pass);
define("DRIVER", "mysql");

class Connection{
    private $host;
    private $port;
    private $db;
    private $user;
    private $pass;
    private $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
    ];

    public function __construct() {
        $this->host = SERVER;
        $this->port = getenv('MYSQLPORT') ?: '3306';
        $this->db = DATABASE;
        $this->user = USER;
        $this->pass = PASSWORD;
    }

    public function connect(){
        $connectionString = DRIVER . ":host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db . "; charset=utf8mb4";
        error_log("Attempting database connection with: " . $connectionString);
        return new \PDO($connectionString, $this->user, $this->pass, $this->options);
    }
}

?>