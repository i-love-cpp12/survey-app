<?php
declare(strict_types=1);
require_once(__DIR__ . "/../entity/survey.php");

namespace app\domain\repository;
use app\domain\entity\Survey;

interface SurveyRepository
{
    public function save(Survey $survey): void;
    public function findSurveyByCode(string $code): ?Survey;
    public function codeExists(string $code): bool;
    /** @return Survey[] */
    public function getSurveys(): array;
}