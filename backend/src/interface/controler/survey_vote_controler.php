<?php
declare(strict_types=1);

namespace app\interface\controler;

require_once(__DIR__ . "/../../application/service/survey_vote_service.php");
require_once(__DIR__ . "/../../application/DTO/voteDTO.php");
require_once(__DIR__ . "/../../infrastructure/http/request.php");

use app\application\service\SurevyVoteService;
use app\application\DTO\VoteDTO;
use app\infrastructure\http\Request;
use app\infrastructure\http\Respond;
use app\shared\exception\AlreadyVotedException;
use app\shared\exception\OptionNotFoundException;
use app\shared\exception\SurveyNotFoundException;
use app\shared\exception\MustNotBeNullException;
use Exception;
use Throwable;

class SurveyVoteControler
{
    function __construct(private SurevyVoteService $surveyVoteService){}

    public function vote(string $surevyCode): void
    {
        $optionId = new Request()->bodyJSON()["optionId"];
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
            $this->handleException($e, $DTO);
        }

        //message to do
        Respond::json(["error" => ""]);

        
    }
    private function handleException(Exception $e, VoteDTO $DTO): void
    {
        if($e instanceof MustNotBeNullException)
            Respond::json(["error" => "Option id must be provided"], 422);
        if($e instanceof SurveyNotFoundException)
            Respond::json(["error" => "Survey with code: $DTO->surveyCode not found"], 404);
        if($e instanceof OptionNotFoundException)
            Respond::json(["error" => "Survey with code: $DTO->surveyCode have not have option with Id: " . $DTO->optionId], 404);
        if($e instanceof AlreadyVotedException)
            Respond::json(["error" => "You can not vote in the same survey twice or more: $DTO->surveyCode"], 400);
        throw $e;
    }
}