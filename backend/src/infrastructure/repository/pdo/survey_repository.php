<?php
declare(strict_types=1);
namespace app\infrastructure\repository\pdo;

require_once(__DIR__ . "/../../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../../domain/entity/survey.php");
require_once(__DIR__ . "/../mapper/option_mapper.php");
require_once(__DIR__ . "/../mapper/survey_mapper.php");

use app\domain\entity\Option;
use app\domain\entity\Survey;
use app\domain\repository\SurveyRepository as SurveyRepositoryInterface;
use app\infrastructure\repository\mapper\OptionMapper;
use app\infrastructure\repository\mapper\SurveyMapper;
use PDO;

class SurveyRepository implements SurveyRepositoryInterface
{
    private array $surveys;
    private PDO $conn;

    function __construct(PDO $PDOConnection)
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
        $this->conn = $PDOConnection;
    }
    //fix so when option id == null then update and if survey id == null add the id
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
        $stmt = $this->conn->prepare("SELECT 1 FROM survey WHERE survey_code = ?;");

        $stmt->execute([$code]);

        return !$stmt->fetchColumn();
    }

    /** @return Survey[] */
    public function getSurveys(): array
    {
        $data = $this->conn->query(
            "SELECT survey.survey_id, survey_code, question, is_active, option_id, `option`.`value` as option_value, votes
            FROM survey
            LEFT JOIN `option` USING(survey_id);"
        )->fetchAll(PDO::FETCH_ASSOC);

        $surveysRaw = [];
        foreach($data as $row)
        {
            $id = $row["survey_id"];
            if(!isset($surveysRaw[$id]))
            {
                $surveysRaw[$id] = [
                    "survey_id" => $id,
                    "survey_code" => $row["survey_code"],
                    "survey_question" => $row["question"],
                    "options" => [],
                    "survey_is_active" => boolval($row["is_active"])
                ];
            }
            if($row["option_id"] !== null)
            {
                $surveysRaw[$id]["options"][] = [
                    "option_id" => $row["option_id"],
                    "option_value" => $row["option_value"],
                    "option_votes" => $row["votes"],
                ];
            }
        }

        $surveys = [];

        foreach($surveysRaw as $surveyId => $surveyBody)
        {
            $surveys[] = SurveyMapper::map(["survey_id" => $surveyId, ...$surveyBody]);
        }
        return $surveys;
    }

    /** @return Option[] */
    public function getSurveyResults(string $code): array | null
    {
        $code = strtoupper($code);

        $stmt = $this->conn->prepare("SELECT `option`.option_id AS id, `option`.`value` AS option_value, `option`.votes AS option_votes FROM `option` JOIN survey USING(survey_id) WHERE survey.survey_code = :code;");

        $stmt->execute(["code" => $code]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach($data as $row)
        {
            $result[] = OptionMapper::map($row);
        }

        return $result ?: null;
    }
}