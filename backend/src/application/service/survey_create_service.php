<?php
declare(strict_types=1);

namespace app\application\service;

require_once(__DIR__ . "/../DTO/create_survey_DTO.php");
require_once(__DIR__ . "/../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../domain/service/survey_generate_code_service.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");

use app\application\DTO\CreateSurveyDTO;
use app\domain\entity\Option;
use app\domain\entity\Survey;
use app\domain\repository\SurveyRepository;
use app\domain\service\SurveyGenerateCodeService;
use app\shared\exception\ValidationException;

class SurveyCreateService
{
    static int $codeLenght = 7;
    function __construct(private SurveyRepository $surveyRepo, private SurveyGenerateCodeService $surveyGenerateCodeService)
    {}
    public function execute(CreateSurveyDTO $DTO): string
    {
        if(!$DTO->question)
            throw new ValidationException("Survey question can not be empty");

        if($DTO->options === null || count($DTO->options) === 0)
            throw new ValidationException("Survey options can not be empty");

        foreach($DTO->options as $option)
        {
            if(empty($option))
                throw new ValidationException("Survey option can not be empty");
        }
        $options = [];
        foreach($DTO->options as $option)
        {
            $options[] = new Option(null, $option);
        }
        
        $survey = new Survey(null, $DTO->question, $this->surveyGenerateCodeService->execute(self::$codeLenght), $options);

        $this->surveyRepo->save($survey);

        return $survey->code;
    }
}