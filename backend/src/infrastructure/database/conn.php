<?php
declare(strict_types=1);

namespace app\infrastructure\database;

require(__DIR__ . "/../../../config/db_config.php");

use \PDO;
use \Exception;
use DBConfig;
use PDOException;

class DBConnection
{
    private PDO $conn;
    private static ?DBConnection $instance = null;

    private function __construct()
    {

        $host = DBConfig::$host;
        $dbname = DBConfig::$dbname;
        $user = DBConfig::$user;
        $password = DBConfig::$password;

        $this->conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public static function getConnection(): PDO
    {
        if(!self::$instance)
            self::$instance = new DBConnection();
        
        return self::$instance->conn;
    }

}