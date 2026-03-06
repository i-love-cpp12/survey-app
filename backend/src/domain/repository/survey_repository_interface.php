<?php
declare(strict_types=1);
require_once("survey.php");

namespace app\domain\repository;
use app\domain\entity\Survey;

interface surveyRepository
{
    public function save(Survey $survey): void;
}