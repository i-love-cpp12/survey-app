<?php
declare(strict_types = 1);

require_once(__DIR__ . "/../../domain/repository/survey_repository.php");
require_once(__DIR__ . "/../../domain/repository/survey_vote_repository.php");
require_once(__DIR__ . "/../DTO/voteDTO.php");
require_once(__DIR__ . "/../DTO/voteDTO.php");
require_once(__DIR__ . "/../../shared/exception/exception.php");

namespace app\application\service;

use app\domain\entity\Survey;
use app\domain\entity\Option;

use app\domain\repository\SurveyRepository;
use app\domain\repository\VoteRepository;

use app\application\DTO\VoteDTO;

use app\domain\value_object\Token;
use app\domain\value_object\User;
use app\domain\value_object\Vote;

use app\shared\exception\SurveyNotFoundException;
use app\shared\exception\AlreadyVotedException;
use app\shared\exception\OptionNotFoundException;

class SurevyVoteService
{
    function __construct(private SurveyRepository $surveyRepo, private VoteRepository $voteRepo){}
    public function execute(VoteDTO $DTO): void
    {
        $survey = $this->surveyRepo->findSurveyByCode($DTO->surveyCode);

        if(!$survey)
            throw new SurveyNotFoundException("Survey with code: " . $DTO->surveyCode . "does not exists");

        $user = new User(new Token($DTO->unhashedToken));

        if(!$survey->findOption($DTO->optionId))
            throw new OptionNotFoundException("Option with id: " . $DTO->optionId . " not found in surevy with code" . $survey->code);

        if($this->voteRepo->hasVoted($survey->getId(), $user))
            throw new AlreadyVotedException("User with token: " . $user->token->value . "already voted in survey with code: " . $survey->code);

        $vote = new Vote(null, $survey->getId(), $DTO->optionId, $user);

        $this->voteRepo->save($vote);
    }
}