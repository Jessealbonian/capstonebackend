<?php

//set default time zone

date_default_timezone_set("Asia/Manila");

//set time limit of requests
set_time_limit(1000);

// Use environment variables for database configuration
$host = getenv('MYSQLHOST') ?: 'crossover.proxy.rlwy.net';
$port = getenv('MYSQLPORT') ?: '3306';
$db   = getenv('MYSQLDATABASE') ?: 'home_hoa';
$user = getenv('MYSQLUSER') ?: 'railway';
$pass = getenv('MYSQLPASSWORD') ?: 'gLjXtuyGRfwgmafkdLUeIvdOqVBspSnI';

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
        return new \PDO($this->connectionString, USER, PASSWORD, $this->options);
    }
}

?>