<?php
declare(strict_types=1);

namespace app\infrastructure\http;

class Respond
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode($data);
    }
}