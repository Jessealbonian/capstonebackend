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
    private $connectionString = DRIVER . ":host=" . SERVER . ";port=" . $port . ";dbname=" . DATABASE . "; charset=utf8mb4";
    private $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
    ];


    public function connect(){
        error_log("Attempting database connection with: " . $this->connectionString);
        return new \PDO($this->connectionString, USER, PASSWORD, $this->options);
    }
}

?>