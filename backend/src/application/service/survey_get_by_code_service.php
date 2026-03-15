<?php
declare(strict_types=1);

namespace app\application\service;

require_once(__DIR__ . "/../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../domain/entity/survey.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");

use app\domain\entity\Survey;
use app\domain\repository\SurveyRepository;
use app\shared\exception\NotFoundException;

class SurveyGetByCodeService
{
    function __construct(private SurveyRepository $surveyRepo){}
    public function execute(string $code): Survey
    {
        $survey = $this->surveyRepo->findSurveyByCode($code);
        if($survey === null)
            throw new NotFoundException("Survey with code: $code does not exists");
        return $survey;
    }
}