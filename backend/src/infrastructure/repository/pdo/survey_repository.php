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
use PDOException;

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

    public function save(Survey $survey): void
    {
        // if($survey->getId() === null)
        // {
        //     $this->surveys[] = $survey;
        //     return;
        // }

        // foreach($this->surveys as $i => $mySurvey)
        // {
        //     if($mySurvey->getId() === $survey->getId())
        //     {
        //         $this->surveys[$i] = $survey;
        //         return;
        //     }
        // }

        // $this->surveys[] = $survey;
        if($survey->getId() === null)
        {
            

            $this->conn->beginTransaction();
            try
            {
                $stmt = $this->conn->prepare("INSERT INTO survey (survey_code, question) VALUES (:code, :question);");
                $stmt->execute(["code" => $survey->code, "question" => $survey->question]);

                $survey->setId(intval($this->conn->lastInsertId()));
                
                foreach($survey->getOptions() as $option)
                {
                    $stmt = $this->conn->prepare("INSERT INTO `option` (survey_id, `value`, votes) VALUES (:id, :option_value, :votes)");
                    $stmt->execute(["id" => $survey->getId(), "option_value" => $option->value, "votes" => $option->getVotesCount()]);
                }
                $this->conn->commit();
                
            }
            catch(PDOException $e)
            {
                $this->conn->rollBack();
                throw $e;
            }
            return;
        }

        $this->conn->beginTransaction();
        try
        {
            $stmt = $this->conn->prepare("UPDATE survey SET survey_code = :code, question = :question, is_active = :active WHERE survey_id = :id;");
            $stmt->execute(["code" => $survey->code, "question" => $survey->question, "active" => $survey->isActive, "id" => $survey->getId()]);
            
            foreach($survey->getOptions() as $option)
            {
                if($option->id !== null)
                {
                    $stmt = $this->conn->prepare("UPDATE `option` SET `value` = :option_value WHERE option_id = :id");
                    $stmt->execute(["option_value" => $option->value, "id" => $option->id]);
                }
                else
                {
                    $stmt = $this->conn->prepare("INSERT INTO `option` (survey_id, `value`, votes) VALUES (:id, :option_value, :votes)");
                    $stmt->execute(["id" => $survey->getId(), "option_value" => $option->value, "votes" => $option->getVotesCount()]);
                }
            }
            $this->conn->commit();
            
        }
        catch(PDOException $e)
        {
            $this->conn->rollBack();
            throw $e;
        }
    }
    
    public function findSurveyByCode(string $code): ?Survey
    {
        $stmt = $this->conn->prepare(
            "SELECT survey.survey_id, survey_code, question, is_active, option_id, `option`.`value` as option_value, votes as votes
            FROM survey
            LEFT JOIN `option` USING(survey_id)
            WHERE survey_code = ?;"
        );

        $stmt->execute([$code]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $survey = ["survey_id" => $data[0]["survey_id"], "survey_code" => $data[0]["survey_code"], "survey_question" => $data[0]["question"], "survey_is_active" => boolval($data[0]["is_active"]), "options" => []];
        foreach($data as $row)
        {
            if($row["option_id"] === null)
                break;    
            
            $survey["options"][] = [
                "option_id" => $row["option_id"],
                "option_value" => $row["option_value"],
                "option_votes" => $row["votes"]
            ];
        }

        return SurveyMapper::map($survey);
    }

    public function codeExists(string $code): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM survey WHERE survey_code = ?;");

        $stmt->execute([$code]);

        return $stmt->fetchColumn() !== false;
    }

    /** @return Survey[] */
    public function getSurveys(): array
    {
        $data = $this->conn->query(
            "SELECT survey.survey_id as survey_id, survey_code, question, is_active, option_id, `option`.`value` as option_value, votes
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