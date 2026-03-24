<?php
declare(strict_types=1);

namespace app\domain\repository;

require_once(__DIR__ . "/../entity/survey.php");

use app\domain\entity\Survey;

interface SurveyRepository
{
    public function save(Survey $survey): void;
    public function findSurveyByCode(string $code): ?Survey;
    public function codeExists(string $code): bool;
    /** @return Survey[] */
    public function getSurveys(): array;

    /** @return Option[] */
    public function getSurveyResults(string $code): array | null;
}