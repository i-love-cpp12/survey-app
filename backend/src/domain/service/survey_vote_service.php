<?php

declare(strict_types=1);

require_once(__DIR__ . "/../entity/survey.php");
require_once(__DIR__ . "/../entity/option.php");
require_once(__DIR__ . "/../value_object/user.php");
require_once(__DIR__ . "/../../shared/exception/domain_exception.php");

namespace app\domain\service;

use app\domain\entity\Survey;
use app\domain\entity\Option;
use app\domain\value_object\User;

class SurveyVoteService
{
    function vote(Survey $survey, Option $option, User $user): void
    {
        $survey->vote($option->id, $user);
    }
}