<?php
declare(strict_types=1);

namespace app\application\service;

require_once(__DIR__ . "/../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../domain/entity/survey.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");

use app\domain\repository\SurveyRepository;
use app\shared\exception\NotFoundException;

class SurveyGetResultsService
{
    function __construct(private SurveyRepository $surveyRepo){}

    /** @return Option[] */
    public function execute(string $code): array
    {
        $results = $this->surveyRepo->getSurveyResults($code);
        if($results === null)
            throw new NotFoundException("Survey with code: $code does not exists");
        return $results;
    }
}