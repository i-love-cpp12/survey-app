<?php
declare(strict_types=1);
namespace app\infrastructure\repository\mapper;

require_once(__DIR__ . "/../../../domain/entity/option.php");

use app\domain\entity\Option;

class OptionMapper
{
    public static function map(array $data): Option
    {
        return new Option($data["option_id"] ?? null, $data["option_value"], $data["option_votes"] ?? 0);
    }
}