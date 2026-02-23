<?php
declare(strict_types=1);

function createConnection(): PDO
{
    $dbinfo = require("dbinfo.php");
    return new PDO("mysql:host={$dbinfo['host']};dbname={$dbinfo['dbname']};charset=utf8", $dbinfo["user"], $dbinfo["password"], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}