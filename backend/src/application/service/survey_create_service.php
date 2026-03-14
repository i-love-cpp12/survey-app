<?php
declare(strict_types=1);

namespace app\application\service;

require_once(__DIR__ . "/../DTO/create_survey_DTO.php");
require_once(__DIR__ . "/../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");

use app\application\DTO\CreateSurveyDTO;
use app\domain\entity\Option;
use app\domain\entity\Survey;
use app\domain\repository\SurveyRepository;
use app\shared\exception\SurveyAlreadyExists;
use app\shared\exception\ValidationException;

class SurveyCreateService
{
    function __construct(private SurveyRepository $surveyRepo){}
    public function execute(CreateSurveyDTO $DTO): void
    {
        if(!$DTO->code)
            throw new ValidationException("Survey code can not be empty");

        if(!$DTO->question)
            throw new ValidationException("Survey question can not be empty");

        if($DTO->options === null || count($DTO->options) === 0)
            throw new ValidationException("Survey options can not be empty");

        if($this->surveyRepo->codeExists($DTO->code))
            throw new SurveyAlreadyExists("Survey with code: $DTO->code already exists");

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
        $survey = new Survey(null, $DTO->question, $DTO->code, $options);

        $this->surveyRepo->save($survey);
    }
}