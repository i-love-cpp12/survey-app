<?php
declare(strict_types=1);
require_once("respond.php");

function validateRequestMethod(string $method): void
{
    $method = strtoupper($method);
    if($_SERVER["REQUEST_METHOD"] !== $method)
    {
        respondWithError("Wrong request method: request method should be $method, {$_SERVER['REQUEST_METHOD']} has been given}", 405);
    }
}

function getRequestBody(bool $json = true): array | string | null
{
    $body = file_get_contents("php://input");
    return $json ? json_decode($body, true) : $body;
}