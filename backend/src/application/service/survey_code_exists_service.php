<?php
declare(strict_types=1);

namespace app\application\service;

require_once(__DIR__ . "/../../domain/repository/survey_repository.php");

use app\domain\repository\SurveyRepository;


class SurveyCodeExistsService
{
    static int $codeLenght = 7;
    function __construct(private SurveyRepository $surveyRepo)
    {}
    public function execute(string $code): bool
    {
        return $this->surveyRepo->codeExists($code);
    }
}