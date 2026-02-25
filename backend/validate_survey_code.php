<?php

declare(strict_types=1);
require_once("respond.php");
require_once("request.php");
require_once("conn.php");
require_once("survey.php");

validateRequestMethod("POST");

$body = getRequestBody();

if(!$body || !isset($body["surveyCode"]) || !is_string($body["surveyCode"]))
    respondWithError(getErrorMessageWrongBodyJSON(["surveyCode" => "string"]), 422, ["isCodeOK" => false]);

$code = $body["surveyCode"];

$pdo = createConnection();

$survey = new Survey($code, $pdo);

$pdo = null;

$isCodeInDB = $survey->validateCode();


if($isCodeInDB)
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
exit();