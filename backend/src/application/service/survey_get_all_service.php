<?php
declare(strict_types=1);

namespace app\application\service;

require_once(__DIR__ . "/../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../domain/entity/survey.php");

use app\domain\entity\Survey;
use app\domain\repository\SurveyRepository;

class SurveyGetAllService
{
    function __construct(private SurveyRepository $surveyRepo){}

    /** @return Survey[] */
    public function execute(): array
    {
        return $this->surveyRepo->getSurveys();
    }
}