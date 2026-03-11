<?php

declare(strict_types=1);

namespace app\domain\service;

require_once(__DIR__ . "/../repository/survey_repository.php");
require_once(__DIR__ . "/../entity/survey.php");
require_once(__DIR__ . "/../../shared/exception/domain_exception.php");


use app\domain\repository\SurveyRepository;
use app\domain\entity\Survey;
use app\shared\exception\ValidationException;

class SurveyGenerateCodeService
{
    function __construct(private SurveyRepository $surveyRepo){}
    function execute(int $codeLenght): string
    {
        if(Survey::validateCodeLenght($codeLenght))
            throw new ValidationException("Code must be (" . Survey::$minCodeSize . " - " . Survey::$maxCodeSize . ") long");
        $generatedCode = "";
        do
        {
            $generatedCode = self::generateCode($codeLenght);
        } while($this->surveyRepo->codeExists($generatedCode));

        return $generatedCode;
    }
    private static function generateCode(int $codeLenght): string
    {
        $generatedCode = "";
        $allowedCharsLenght = strlen(Survey::$allowedCodeChars);
        for($i = 0; $i < $codeLenght; ++$i)
        {
            $generatedCode .= Survey::$allowedCodeChars[random_int(0, $allowedCharsLenght - 1)]; 
        }
        return $generatedCode;
    }
}