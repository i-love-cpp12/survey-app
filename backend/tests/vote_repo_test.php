<?php

require_once(__DIR__ . "/../src/infrastructure/repository/pdo/survey_vote_repository.php");
require_once(__DIR__ . "/../src/infrastructure/database/conn.php");
require_once(__DIR__ . "/../src/domain/value_object/vote.php");
require_once(__DIR__ . "/../src/domain/value_object/token.php");
require_once(__DIR__ . "/../src/domain/value_object/user.php");

use app\domain\value_object\Token;
use app\domain\value_object\User;
use app\domain\value_object\Vote;
use app\infrastructure\database\DBConnection;
use app\infrastructure\repository\pdo\SurveyVoteRepository;


$conn = DBConnection::getConnection();

$repo = new SurveyVoteRepository($conn);

$repo->save(new Vote(3, 3, 1, new User(new Token($_SERVER["HTTP_USER_AGENT"] . $_SERVER["REMOTE_ADDR"]))));
