<?php
declare(strict_types=1);

namespace app\domain\repository;

require_once(__DIR__ . "/../value_object/vote.php");
require_once(__DIR__ . "/../value_object/user.php");
require_once(__DIR__ . "/../value_object/user.php");

use app\domain\value_object\Vote;
use app\domain\value_object\User;

interface VoteRepository
{
    public function save(Vote $vote): void;
    public function hasVoted(string $surveyCode, User $user): bool;
    /** @return Option[] */
    public function getSurveyResults(string $surveyCode): array;
}