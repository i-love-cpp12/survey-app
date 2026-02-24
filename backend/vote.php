<?php
//TO DO: cookies do not work!!!
declare(strict_types=1);

require_once("survey.php");
require_once("request.php");
require_once("respond.php");
require_once("token.php");
require_once("conn.php");

validateRequestMethod("POST");
$body = getRequestBody();

if(!$body || !isset($body["surveyCode"]) || !isset($body["optionId"]))
    respondWithError("Wrong body fromat. Should be JSON with surveyCode and optionId property", 422, ["voted" => false]);

$body["surveyCode"] = strtoupper($body["surveyCode"]);
$body["optionId"] = is_numeric($body["optionId"]) ? $body["optionId"] : intval($body["optionId"]);

$pdo = createConnection();
$token = "";
if (!($token = setToken($pdo)))
{
    $pdo = null;
    respondWithError("Unable to asign token", 500, ["voted" => false]);
}

$survey = new Survey($body["surveyCode"], $pdo);

if(!$survey->validateCode() || !$survey->vote($body["optionId"], $token, $pdo))
    respondWithError("Vote atempt was unsuccesful", 400, ["voted" => false]);
$pdo = null;
respond(["error" => "", "voted" => true]);


