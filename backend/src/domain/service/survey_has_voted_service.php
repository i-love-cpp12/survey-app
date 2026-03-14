<?php
declare(strict_types=1);

namespace app\domain\service;

require_once(__DIR__ . "/../repository/survey_vote_repository.php");
require_once(__DIR__ . "/../../application/DTO/has_voted_DTO.php");

use app\application\DTO\HasVotedDTO;
use app\domain\repository\VoteRepository;
use app\domain\value_object\Token;
use app\domain\value_object\User;

class SurveyHasVotedService
{
    function __construct(private VoteRepository $voteRepo){}
    function execute(HasVotedDTO $DTO): bool
    {
        $user = new User(new Token($DTO->unhashedToken));

        return $this->voteRepo->hasVoted($DTO->surveyCode, $user);
    }
}