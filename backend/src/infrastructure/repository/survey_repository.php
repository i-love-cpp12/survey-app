<?php
declare(strict_types=1);
namespace app\infrastructure\repository;

require_once(__DIR__ . "/../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../domain/entity/surevy.php");

use app\domain\entity\Survey;
use app\domain\repository\SurveyRepository;


class DummySurveyRepository implements SurveyRepository
{
    public function save(Survey $survey): void
    {
        return;
    }
    public function findSurveyByCode(string $code): ?Survey
    {
        return null;
    }
    public function codeExists(string $code): bool
    {
        return true;
    }
    /** @return Survey[] */
    public function getSurveys(): array
    {
        return [];
    }
}