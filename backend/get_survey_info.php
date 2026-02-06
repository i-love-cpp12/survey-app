<?php

declare(strict_types=1);
require_once("respond.php");

if($_SERVER["REQUEST_METHOD"] !== "POST")
{
    respondWithError("Wrong request method: request method should be POST, {$_SERVER['REQUEST_METHOD']} has been given}", 405, ["surveyInfo" => []]);
}
$body = json_decode(file_get_contents("php://input"), true);

if(!isset($body["code"]))
{
    respondWithError("Code must be given", 422, ["surveyInfo" => []]);
}

$code = $body["code"];

$dbinfo = require_once("dbinfo.php");
$pdo = new PDO("mysql:host={$dbinfo['host']};dbname={$dbinfo['dbname']};charset=utf8", $dbinfo["user"], $dbinfo["password"], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$stmt = $pdo->prepare("SELECT s.survey_id as survey_id, s.survey_code as survey_code, s.question as question, o.option_id as option_id, o.value as option_value FROM survey as s JOIN option as o USING(survey_id) WHERE survey_code = :code AND is_active = 1");
$stmt->execute(["code" => $code]);
$pdo = null;

if(!$stmt->rowCount())
{
    respondWithError("There is not an active survey with code: $code", 400, ["surveyInfo" => []]);
}

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$options = [];

foreach($data as $row)
{
    $options[] = [
        "id" => $row["option_id"],
        "value" => $row["option_value"]
    ];
}

$data = [
    "survey_id" => $data[0]["survey_id"],
    "survey_code" => $data[0]["survey_code"],
    "question" => $data[0]["question"],
    "options" => $options
];

respond($data);
exit();