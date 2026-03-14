<?php
declare(strict_types=1);

namespace app\interface\controler;

require_once(__DIR__ . "/../../application/service/survey_vote_service.php");
require_once(__DIR__ . "/../../application/DTO/vote_DTO.php");
require_once(__DIR__ . "/../../application/DTO/has_voted_DTO.php");
require_once(__DIR__ . "/../../infrastructure/http/request.php");
require_once(__DIR__ . "/../../infrastructure/http/exception_handler.php");

use app\application\service\SurveyVoteService;
use app\application\DTO\HasVotedDTO;
use app\application\DTO\VoteDTO;
use app\domain\service\SurveyHasVotedService;
use app\infrastructure\http\ExceptionHandler;
use app\infrastructure\http\Request;
use app\infrastructure\http\Respond;
use Throwable;

class SurveyVoteControler
{
    function __construct(
        private SurveyVoteService $surveyVoteService,
        private SurveyHasVotedService $surveyHasVotedService
    ){}

    public function vote(string $surevyCode): void
    {
        $body = (new Request())->bodyJSON();
        $optionId = $body["optionId"] ?? null;
        //chech type
        $rowToken = $_SERVER["HTTP_USER_AGENT"] . $_SERVER["REMOTE_ADDR"];

        $DTO = new VoteDTO(
            $surevyCode,
            $optionId,
            $rowToken
        );

        try
        {
            $this->surveyVoteService->execute($DTO);
        }
        catch(Throwable $e)
        {
            ExceptionHandler::handle($e);
        }

        Respond::json(["error" => ""]);

        
    }
    public function hasVoted(string $surevyCode): void
    {
        $rowToken = $_SERVER["HTTP_USER_AGENT"] . $_SERVER["REMOTE_ADDR"];

        $DTO = new HasVotedDTO(
            $surevyCode,
            $rowToken
        );

        $hasVoted = null;
        try
        {
            $hasVoted = $this->surveyHasVotedService->execute($DTO);
        }
        catch(Throwable $e)
        {
            ExceptionHandler::handle($e);
        }

        Respond::json(["error" => "", "data" => [
            "hasVoted" => $hasVoted
        ]]);
    }
}