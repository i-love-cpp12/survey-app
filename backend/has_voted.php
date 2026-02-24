<?php
declare(strict_types=1);
require_once("request.php");
require_once("token.php");
require_once("conn.php");
require_once("survey.php");

validateRequestMethod("POST");

$body = getRequestBody();
if(!isset($_COOKIE["token"]))
{
    $pdo = createConnection();
    setToken($pdo);
    $pdo = null;
    respond(["error" => "", "hasVoted" => false]);
}
if(!$body || !isset($body["surveyCode"]) || !is_string($body["surveyCode"]))
    respondWithError(getErrorMessageWrongBodyJSON(["surveyCode" => "string"]), 422, ["isCodeOK" => false]);

$pdo = createConnection();
$survey = new Survey($body["surveyCode"], $pdo);
$hasVoted = $survey->hasVoted($_COOKIE["token"], $pdo);
$pdo = null;

respond(["error" => "", "hasVoted" => $hasVoted]);