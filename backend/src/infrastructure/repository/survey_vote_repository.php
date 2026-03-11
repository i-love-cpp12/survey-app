<?php
declare(strict_types=1);
namespace app\infrastructure\repository;

require_once(__DIR__ . "/../../domain/repository/survey_vote_repository.php");
require_once(__DIR__ . "/../../domain/value_object/vote.php");
require_once(__DIR__ . "/../../domain/value_object/user.php");
require_once(__DIR__ . "/../../domain/entity/option.php");

use app\domain\repository\VoteRepository;
use app\domain\value_object\User;
use app\domain\value_object\Vote;
use app\domain\entity\Option;

class DummySurveyVoteRepository implements VoteRepository
{
    public function save(Vote $vote): void
    {
        return;
    }
    public function hasVoted(int $surveyId, User $user): bool
    {
        return false;
    }
    /** @return Option[] */
    public function getSurveyResults(int $surveyId): array
    {
        return [];
    }
}