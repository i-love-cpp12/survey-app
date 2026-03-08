<?php
declare(strict_types=1);
require_once(__DIR__ . "/../../application/service/survey_vote_service.php");
require_once(__DIR__ . "/../../application/DTO/voteDTO.php");
require_once(__DIR__ . "/../../infrastructure/http/request.php");

namespace app\interface\controler;

use app\application\DTO\VoteDTO;
use app\domain\value_object\Vote;
use app\infrastructure\http\Request;

class SurveyVoteControler
{
    function __construct(private SurevyVoteService $surveyVoteService){}

    public function vote(int $surevyCode): void
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
            $surveyVoteService->vote($DTO);
        }
        catch()
    }
}