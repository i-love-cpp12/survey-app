<?php
declare(strict_types=1);

function respondWithError(string $errorMsg, int $httpResponseCode = 404, array $additionalResponceInfo = []): never
{
    header("Content-Type: application/json");
    http_response_code($httpResponseCode);
    $data =
    [
        "error" => $errorMsg
    ];
    foreach($additionalResponceInfo as $key => $value)
    {
        $data[$key] = $value;
    }

    echo json_encode($data);
    die();
}
function respond(array $responceData = [], int $httpResponseCode = 200): never
{
    header("Content-Type: application/json");
    http_response_code($httpResponseCode);

    echo json_encode($responceData);
    die();
}