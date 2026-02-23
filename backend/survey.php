<?php
require_once("conn.php");
class Survey
{
    private bool $isCodeValid = true;
    private ?array $data = null;
    public function __construct(string $code, PDO $pdo)
    {
        $stmt = $pdo->prepare("SELECT s.survey_id as survey_id, s.survey_code as survey_code, s.question as question, o.option_id as option_id, o.value as option_value FROM survey as s JOIN `option` as o USING(survey_id) WHERE survey_code = :code AND is_active = 1");
        $stmt->execute(["code" => $code]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$data)
            $this->isCodeValid = false;
        else
        {
            $options = [];

            foreach($data as $row)
            {
                $options[] = [
                    "id" => $row["option_id"],
                    "value" => $row["option_value"]
                ];
            }
            $this->data = [
                "surveyId" => $data[0]["survey_id"],
                "surveyCode" => $data[0]["survey_code"],
                "question" => $data[0]["question"],
                "options" => $options
            ];
        }
    }
    public function validateCode(): bool
    {
        return $this->isCodeValid;
    }

    public function getData(): array | null
    {
        return $this->data;
    }

    public function vote(int $optionId, string $userIp, PDO $pdo): bool
    {
        if(!$this->isOptionFromSurvey($optionId) ||
            !$this->validateIp($userIp) ||
            $this->hasVoted($userIp, $pdo))
                return false;

        $stmt = $pdo->prepare("INSERT INTO vote (user_ip, option_id) VALUES (:userIp, :optionId)");
        $stmt->execute(["userIp" => $userIp, "optionId" => $optionId]);
        return true;
    }

    public function hasVoted(string $userIp, PDO $pdo): bool
    {
        $stmt = $pdo->prepare("SELECT s.survey_code FROM survey AS s JOIN `option` AS o USING(survey_id) JOIN vote AS v USING(option_id) WHERE v.user_ip = :ip AND s.survey_code = :code;");
        $stmt->execute(["ip" => $userIp, "code" => $this->data["code"]]);

        return $stmt->rowCount() === 1;
    }

    public function isOptionFromSurvey(int $optionId): bool
    {
        return in_array($optionId, $this->data["options"]);
    }

    private function validateIp(string $userIp): bool
    {
        return preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $userIp);
    }
    
}