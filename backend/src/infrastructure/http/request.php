<?php
declare(strict_types=1);

namespace app\infrastructure\http;

use LogicException;

class Request
{
    readonly public string $method;
    readonly public string $uri;

    function __construct()
    {
        $this->method = strtoupper($_SERVER["REQUEST_METHOD"]);
        $this->uri = $_SERVER["REQUEST_URI"];
    }
    
    function bodyJSON(): array | null
    {
        if($this->method === "GET")
            throw new LogicException("GET method does not have body");
        return json_decode(file_get_contents("php://input"), true);
    }
}