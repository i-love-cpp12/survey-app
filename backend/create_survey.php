<?php

declare(strict_types=1);

require_once("request.php");
require_once("respond.php");
require_once("survey.php");
require_once("conn.php");

validateRequestMethod("POST");

$body = getRequestBody();

if(
    !$body ||
    !isset($body["question"]) ||
    !is_string($body["question"]) ||
    $body["question"] === "" ||
    !isset($body["options"]) ||
    !is_array($body["options"]) ||
    count($body["options"]) < 2
)
{
    respondWithError(getErrorMessageWrongBodyJSON(["question" => "string", "options" => "array[string]"]), 422, ["surveyInfo" => []]);
}
foreach($body["options"] as $option)
{
    if(!is_string($option) || $option === "")
        respondWithError(getErrorMessageWrongBodyJSON(["question" => "string", "options" => "array[string]"]), 422, ["surveyInfo" => []]);
}

$pdo = createConnection();
$survey = Survey::createSurvey($body["question"], $body["options"], $pdo);
$pdo = null;
if(!$survey || !$survey->validateCode())
    respondWithError("Something went wrong while creating survey", 200, ["surveyInfo" => []]);

respond(["error" => "", "surveyInfo" => $survey->getData()]);