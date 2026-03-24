<?php
declare(strict_types=1);
namespace app\infrastructure\http;

require_once(__DIR__ . "/respond.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");

use app\shared\exception\SurveyException;
use PDOException;
use Throwable;

class ExceptionHandler
{
    static public function handle(Throwable $e): void
    {
        if($e instanceof SurveyException)
            Respond::json(["error" => $e->getMessage()], $e->getCode());
        // if($e instanceof PDOException)
        //     Respond::json(["error" => "Something went wrong"], 500);
        if($e instanceof PDOException)
            Respond::json(["error" => "Something went wrong: $e"], 500);
        throw $e;
    }
}