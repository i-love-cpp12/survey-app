<?php

class Survey
{
    private bool $isCodeValid = true;
    private ?array $data = null;
    private static string $codeChars = "qwertyuiopasdfghjklzxcvbnm1234567890";
    public static function createSurvey(string $question, array $options, PDO $pdo): Survey | null
    {
        $surveyCode = Survey::generateSurveyCode($pdo, 7);
        if(strlen($surveyCode) < 4 || $question === "" || count($options) < 2) return null;
        foreach($options as $option)
        {
            if(!is_string($option) || $option === "") return null;  
        }

        $stmt = $pdo->prepare("INSERT INTO survey(survey_code, question) VALUES (:surveyCode, :question);");
        $stmt->execute(["surveyCode" => $surveyCode, "question" => $question]);

        $surveyId = intval($pdo->lastInsertId());

        foreach($options as $option)
        {
            $stmt = $pdo->prepare("INSERT INTO option(survey_id, `value`) VALUES (:surveyId, :optionValue);");
            $stmt->execute(["surveyId" => $surveyId, "optionValue" => $option]);
        }
        return new Survey($surveyCode, $pdo);
    }
    private static function generateSurveyCode(PDO $pdo, int $lenght = 7, int $maxTryesCount = 10): string
    {
        $result = null;
        for($attempts = 0; ($result === null || $pdo->query("SELECT 1 FROM survey WHERE UPPER(survey_code) = UPPER('$result');")->rowCount() !== 0); $attempts++)
        {
            if($attempts >= $maxTryesCount) return "";
            for($i = 0; $i < $lenght; ++$i)
            {
                $result .= strtoupper(Survey::$codeChars[random_int(0, strlen(Survey::$codeChars) - 1)]); 
            }
        }
        return $result;
        
    }
    public function __construct(string $code, PDO $pdo)
    {
        $stmt = $pdo->prepare("SELECT s.survey_id as survey_id, s.survey_code as survey_code, s.question as question, o.option_id as option_id, o.value as option_value, COUNT(v.vote_id) as votes_count FROM survey as s JOIN `option` as o USING(survey_id) LEFT JOIN vote as v USING(option_id) WHERE UPPER(survey_code) = UPPER(:code) AND is_active = 1 GROUP BY o.option_id ORDER BY o.option_id;");
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
                    "value" => $row["option_value"],
                    "votesCount" => $row["votes_count"]
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

    public function vote(int $optionId, string $userToken, PDO $pdo): bool
    {
        if(!$this->isOptionFromSurvey($optionId) || $this->hasVoted($userToken, $pdo))
            return false;
        $stmt = $pdo->prepare("INSERT INTO vote (user_token, option_id) VALUES (:userToken, :optionId)");
        $stmt->execute(["userToken" => $userToken, "optionId" => $optionId]);
        return true;
    }

    public function hasVoted(string $userToken, PDO $pdo): bool
    {        
        $stmt = $pdo->prepare("SELECT s.survey_code FROM survey AS s JOIN `option` AS o USING(survey_id) JOIN vote AS v USING(option_id) WHERE v.user_token = :token AND UPPER(s.survey_code) = UPPER(:code);");
        $stmt->execute(["token" => $userToken, "code" => $this->data["surveyCode"]]);
        return $stmt->rowCount() > 0;
    }

    public function isOptionFromSurvey(int $optionId): bool
    {
        foreach($this->data["options"] as $option)
        {
            if ($option["id"] === $optionId) return true;
        }
        return false;
    }
}