<?php
declare(strict_types = 1);

namespace app\application\service;

require_once(__DIR__ . "/../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../domain/repository/survey_vote_repository.php");
require_once(__DIR__ . "/../DTO/voteDTO.php");
require_once(__DIR__ . "/../DTO/voteDTO.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");


use app\domain\repository\SurveyRepository;
use app\domain\repository\VoteRepository;

use app\application\DTO\VoteDTO;
use app\domain\value_object\Token;
use app\domain\value_object\User;
use app\domain\value_object\Vote;

use app\shared\exception\SurveyNotFoundException;
use app\shared\exception\AlreadyVotedException;
use app\shared\exception\OptionNotFoundException;
use app\shared\exception\ValidationException;

class SurveyVoteService
{
    function __construct(private SurveyRepository $surveyRepo, private VoteRepository $voteRepo){}
    public function execute(VoteDTO $DTO): void
    {
        if(!$DTO->optionId)
            throw new ValidationException("optionId was not provieded");

        $survey = $this->surveyRepo->findSurveyByCode($DTO->surveyCode);

        if(!$survey || $survey->getId() === null)
            throw new SurveyNotFoundException("Survey with code: " . $DTO->surveyCode . "does not exists");

        $user = new User(new Token($DTO->unhashedToken));

        if(!$survey->findOption($DTO->optionId))
            throw new OptionNotFoundException("Option with id: " . $DTO->optionId . " not found in surevy with code" . $survey->code);

        if($this->voteRepo->hasVoted($survey->getId(), $user))
            throw new AlreadyVotedException("User with token: " . $user->token->value . "already voted in survey with code: " . $survey->code);

        $vote = new Vote(null, $survey->getId(), $DTO->optionId, $user);

        $survey->vote($DTO->optionId);
        $this->voteRepo->save($vote);
    }
}