<?php
declare(strict_types=1);

namespace app\infrastructure\database;

require(__DIR__ . "/../../../config/database.php");

use \PDO;
use \Exception;
use DBConfig;
use PDOException;

class DB
{
    private PDO $conn;
    private static ?DB $instance = null;

    private function __construct()
    {

        $host = DBConfig::$host;
        $dbname = DBConfig::$dbname;
        $user = DBConfig::$user;
        $password = DBConfig::$password;

        try
        {
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            throw new Exception("Connection could not be established");
        }
    }
    public static function getConnection(): PDO
    {
        if(!self::$instance)
            self::$instance = new DB();
        
        return self::$instance->conn;
    }

}