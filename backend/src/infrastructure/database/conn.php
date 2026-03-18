<?php

namespace app\infrastructure\database;

require(__DIR__ . "/../../../config/database.php");

use \PDO;
use DBConfig;

class DB
{
    private ?PDO $conn = null;
    private static ?DB $instance = null;

    private function __consturct()
    {

        $host = DBConfig::config["host"];
        $dbname = DBConfig::config["dbname"];
        $user = DBConfig::config["user"];
        $password = DBConfig::config["password"];

        $this->conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    }
    public static function getConnection(): PDO
    {
        if(!self::$instance)
        {
            self::$instance = new DB();
        }
        return self::$instance->conn;
    }

}