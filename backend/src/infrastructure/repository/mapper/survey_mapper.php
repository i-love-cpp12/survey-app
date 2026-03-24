<?php
declare(strict_types=1);
namespace app\infrastructure\repository\mapper;

require_once(__DIR__ . "/../../../domain/entity/survey.php");

use app\domain\entity\Survey;

class SurveyMapper
{
    public static function map(array $data): Survey
    {
        $options = [];
        foreach($data["options"] as $option)
        {
            $options[] = OptionMapper::map($option);   
        }
        return new Survey($data["survey_id"] ?? null, $data["survey_question"], $data["survey_code"], $options, $data["survey_is_active"] ?? null);
    }
}