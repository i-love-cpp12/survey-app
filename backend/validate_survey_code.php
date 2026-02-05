<?php

declare(strict_types=1);
require_once("respond.php");

// die("czesvasdsad");
if($_SERVER["REQUEST_METHOD"] !== "POST")
{
    respondWithError("Wrong request method: request method should be POST, {$_SERVER['REQUEST_METHOD']} has been given}", 405, ["isCodeOK" => false]);
}
$body = json_decode(file_get_contents("php://input"), true);
// echo json_encode(["body" => $body]);
// echo json_encode(["input" => file_get_contents("php://input")]);
// exit();
if(!isset($body["code"]))
{
    respondWithError("Code must be given", 422, ["isCodeOK" => false]);
}

$code = $body["code"];

$dbinfo = require_once("dbinfo.php");
$pdo = new PDO("mysql:host={$dbinfo['host']};dbname={$dbinfo['dbname']};charset=utf8", $dbinfo["user"], $dbinfo["password"], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$stmt = $pdo->prepare("SELECT * FROM survey WHERE survey_code = :code AND is_active = 1;");
$stmt->execute(["code" => $code]);
$pdo = null;

if($stmt->rowCount())
{
    respond([
        "error" => "",
        "isCodeOk" => true
    ]);
}
respond([
        "error" => "",
        "isCodeOk" => false
    ]);
// echo(json_encode($data));
exit();