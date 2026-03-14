<?php
declare(strict_types=1);
namespace app\infrastructure\repository\dummy;

require_once(__DIR__ . "/../../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../../domain/entity/survey.php");

use app\domain\entity\Option;
use app\domain\entity\Survey;
use app\domain\repository\SurveyRepository;


class DummySurveyRepository implements SurveyRepository
{
    private array $surveys;
        
    function __construct()
    {
        $this->surveys = [
            (new Survey(1, "Pierwsza ankieta", "AAAAA", [
                (new Option(1, "opt1", 1)),
                (new Option(2, "opt2", 10)),
                (new Option(3, "opt3", 0))
            ])),
            (new Survey(2, "druga ankieta", "BBBBB", [
                (new Option(4, "opt2.1", 1)),
                (new Option(5, "opt2.2", 10)),
                (new Option(6, "opt2.3", 0))
            ]))
        ];
    }
    public function save(Survey $survey): void
    {
        if($survey->getId() === null)
        {
            $this->surveys[] = $survey;
            return;
        }

        foreach($this->surveys as $i => $mySurvey)
        {
            if($mySurvey->getId() === $survey->getId())
            {
                $this->surveys[$i] = $survey;
                return;
            }
        }

        $this->surveys[] = $survey;
    }
    public function findSurveyByCode(string $code): ?Survey
    {
        $code = strtoupper($code);
        foreach($this->surveys as $survey)
        {
            if($survey->code === $code)
                return $survey;
        }
        return null;
    }
    
    public function codeExists(string $code): bool
    {
        $code = strtoupper($code);
        foreach($this->surveys as $survey)
        {
            if($survey->code === $code)
                return true;
        }
        return false;
    }

    /** @return Survey[] */
    public function getSurveys(): array
    {
        return $this->surveys;
    }
}