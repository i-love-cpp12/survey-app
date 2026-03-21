<?php
declare(strict_types=1);
namespace app\infrastructure\repository\dummy;

require_once(__DIR__ . "/../../../domain/repository/survey_vote_repository.php");
require_once(__DIR__ . "/../../../domain/value_object/vote.php");
require_once(__DIR__ . "/../../../domain/value_object/user.php");
require_once(__DIR__ . "/../../../domain/value_object/token.php");
require_once(__DIR__ . "/../../../domain/entity/option.php");

use app\domain\repository\VoteRepository;
use app\domain\value_object\User;
use app\domain\value_object\Vote;
use app\domain\value_object\Token;

class DummySurveyVoteRepository implements VoteRepository
{
    private array $votes;
    
    function __construct()
    {
        $this->votes = [
            new Vote(1, 1, 2, new User(new Token($_SERVER["HTTP_USER_AGENT"] . $_SERVER["REMOTE_ADDR"]))),
            new Vote(2, 2, 1, new User(new Token("token")))
        ];
    }
    public function save(Vote $vote): void
    {
        if($vote->getId() === null)
            $votes[] = $vote;
            return;

        foreach($this->votes as $i => $myVote)
        {
            if($myVote->getId() === $survey->getId())
            {
                $votes[$i] = $vote;
                return;
            }
        }

        $votes[] = $votes;
    }
    public function hasVoted(string $surveyCode, User $user): bool
    {
        return false;
    }
}