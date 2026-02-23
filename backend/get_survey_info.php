<?php

declare(strict_types=1);
require_once("respond.php");
require_once("request.php");
require_once("conn.php");
require_once("survey.php");

validateRequestMethod("POST");
$body = getRequestBody();

if(!isset($body["code"]))
{
    respondWithError("Code must be given", 422, ["surveyInfo" => []]);
}

$code = strtoupper($body["code"]);

$pdo = createConnection();
$survey = new Survey($code, $pdo);
$pdo = null;

if(!$survey->validateCode())
    respond(["error" => "There is not an active survey with code: $code", "surveyInfo" => []], 200);

$data = $survey->getData();
$respondData = [
    "error" => "",
    "surveyInfo" => [
        "surveyId" => $data["surveyId"],
        "surveyCode" => $data["surveyCode"],
        "question" => $data["question"],
        "options" => $data["options"]
    ]
];

respond($respondData);
exit();