<?php
declare(strict_types=1);

namespace app\interface\controler;

require_once(__DIR__ . "/../../application/service/survey_get_by_code_service.php");
require_once(__DIR__ . "/../../infrastructure/http/respond.php");
require_once(__DIR__ . "/../../infrastructure/http/exception_handler.php");

use app\application\service\SurveyGetAllService;
use app\application\service\SurveyGetByCodeService;
use app\domain\entity\Survey;
use app\infrastructure\http\ExceptionHandler;
use app\infrastructure\http\Respond;
use Throwable;

class SurveyControler
{
    function __construct(
        private SurveyGetByCodeService $surveyGetByCodeService,
        private SurveyGetAllService $surveyGetAllService){}

    public function getByCode($code): void
    {
        $survey = null;
        try
        {
            $survey = $this->surveyGetByCodeService->execute($code);
        }
        catch(Throwable $e)
        {
            ExceptionHandler::handle($e);
        }

        Respond::json([
            "error" => "",
            "data" => $this->surveyToPayLoad($survey)
        ]);
    }
    public function getAll(): void
    {
        $surveys = null;
        try
        {
            $surveys = $this->surveyGetAllService->execute();
        }
        catch(Throwable $e)
        {
            ExceptionHandler::handle($e);
        }

        $payload = [];

        foreach($surveys as $survey)
        {
            $payload[] = $this->surveyToPayLoad($survey);    
        }

        Respond::json([
            "error" => "",
            "data" => $payload
        ]);
    }
    private function surveyToPayLoad(Survey $survey): array
    {
        $suveyEntity = [
            "id" => $survey->getId(),
            "question" => $survey->question,
            "code" => $survey->code,
            "isActive" => $survey->isActive
        ];

        $options = [];
        foreach($survey->getOptions() as $option)
        {
            $options[] = [
                "id" => $option->id,
                "value" => $option->value,
                "votesCount" => $option->getVotesCount()
            ];
        }

        return [
            "survey" => $suveyEntity,
            "options" => $options
        ];
    }
}