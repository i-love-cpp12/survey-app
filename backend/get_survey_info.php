<?php

declare(strict_types=1);
require_once("respond.php");
require_once("request.php");
require_once("conn.php");
require_once("survey.php");

validateRequestMethod("POST");
$body = getRequestBody();

if(!$body || !isset($body["surveyCode"]) || !is_string($body["surveyCode"]))
    respondWithError(getErrorMessageWrongBodyJSON(["surveyCode" => "string"]), 422, ["surveyInfo" => []]);

$code = strtoupper($body["surveyCode"]);

$pdo = createConnection();
$survey = new Survey($code, $pdo);
$pdo = null;

if(!$survey->validateCode())
    respondWithError("There is not an active survey with code: $code", 200, ["surveyInfo" => []]);

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