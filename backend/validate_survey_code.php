<?php

declare(strict_types=1);
require_once("respond.php");
require_once("request.php");
require_once("conn.php");
require_once("survey.php");

validateRequestMethod("POST");

$body = getRequestBody();

if(!$body || !isset($body["code"]))
    respondWithError("Wrong body fromat. Should be JSON with code property", 422, ["isCodeOK" => false]);

$code = strtoupper($body["code"]);

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