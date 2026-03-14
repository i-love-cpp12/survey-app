<?php
declare(strict_types=1);

namespace app\interface\controler;

require_once(__DIR__ . "/../../application/service/survey_vote_service.php");
require_once(__DIR__ . "/../../application/DTO/voteDTO.php");
require_once(__DIR__ . "/../../infrastructure/http/request.php");
require_once(__DIR__ . "/../../infrastructure/http/exception_handler.php");

use app\application\service\SurveyVoteService;
use app\application\DTO\VoteDTO;
use app\infrastructure\http\ExceptionHandler;
use app\infrastructure\http\Request;
use app\infrastructure\http\Respond;
use Throwable;

class SurveyVoteControler
{
    function __construct(private SurveyVoteService $surveyVoteService){}

    public function vote(string $surevyCode): void
    {
        $body = (new Request())->bodyJSON();
        $optionId = $body["optionId"] ?? null;

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
}