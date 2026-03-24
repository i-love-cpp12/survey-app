<?php
declare(strict_types=1);

namespace app\interface\controler;

require_once(__DIR__ . "/../../application/service/survey_get_by_code_service.php");
require_once(__DIR__ . "/../../application/service/survey_get_results_service.php");
require_once(__DIR__ . "/../../application/service/survey_create_service.php");
require_once(__DIR__ . "/../../infrastructure/http/respond.php");
require_once(__DIR__ . "/../../infrastructure/http/request.php");
require_once(__DIR__ . "/../../infrastructure/http/exception_handler.php");
require_once(__DIR__ . "/../../domain/entity/survey.php");
require_once(__DIR__ . "/../../domain/entity/option.php");
require_once(__DIR__ . "/../../application/DTO/create_survey_DTO.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");

use app\application\service\SurveyCreateService;
use app\application\service\SurveyGetAllService;
use app\application\service\SurveyGetByCodeService;
use app\application\service\SurveyGetResultsService;
use app\domain\entity\Survey;
use app\domain\entity\Option;
use app\infrastructure\http\ExceptionHandler;
use app\infrastructure\http\Request;
use app\infrastructure\http\Respond;
use app\application\DTO\CreateSurveyDTO;
use app\shared\exception\ValidationException;
use Throwable;

class SurveyControler
{
    function __construct(
        private SurveyGetByCodeService $surveyGetByCodeService,
        private SurveyGetAllService $surveyGetAllService,
        private SurveyGetResultsService $surveyGetResultsService,
        private SurveyCreateService $surveyCreateService){}

    public function getByCode(string $code): void
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
            $payload[] = $this->surveyToPayload($survey);    
        }

        Respond::json([
            "error" => "",
            "data" => $payload
        ]);
    }
    public function getResults(string $code): void
    {
        $results = null;
        try
        {
            $results = $this->surveyGetResultsService->execute($code);
        }
        catch(Throwable $e)
        {
            ExceptionHandler::handle($e);
        }

        Respond::json([
            "error" => "",
            "data" => $this->optionsToPayload($results)
        ]);
    }
    public function create(): void
    {
        $body = (new Request)->bodyJSON();
        
        $question = $body["question"] ?? null;
        $options = $body["options"] ?? null;
        
        $generatedCode = "";
        try
        {
            if($body === null)
                throw new ValidationException("request body must be provided");
            if($question !== null && !is_string($question))
                throw new ValidationException("question must be type string");

            if($options !== null && !is_array($options))
                throw new ValidationException("options must be type array");

            if($options !== null)
            {
                foreach($options as $option)
                {
                    if(!is_string($option))
                        throw new ValidationException("options elements must be type string");
                }
            }
            
            
            $DTO = new CreateSurveyDTO($question, $options);
            $generatedCode = $this->surveyCreateService->execute($DTO);
        }
        catch(Throwable $e)
        {
            ExceptionHandler::handle($e);
        }

        Respond::json(["error" => "", "data" => ["code" => $generatedCode]]);
    }
    /** @param Option[] */
    private function optionsToPayload(array $options): array
    {
        $result = [];
        foreach($options as $option)
        {
            $result[] = [
                "id" => $option->id,
                "value" => $option->value,
                "votesCount" => $option->getVotesCount()
            ];
        }
        return $result;
    }
    private function surveyToPayload(Survey $survey): array
    {
        $suveyEntity = [
            "id" => $survey->getId(),
            "code" => $survey->code,
            "question" => $survey->question,
            "isActive" => $survey->isActive
        ];

        $options = $this->optionsToPayload($survey->getOptions());

        return [
            ...$suveyEntity,
            "options" => $options
        ];
    }
}