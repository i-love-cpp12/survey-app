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
}