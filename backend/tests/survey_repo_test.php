<?php

require_once(__DIR__ . "/../src/infrastructure/repository/pdo/survey_repository.php");
require_once(__DIR__ . "/../src/infrastructure/database/conn.php");
require_once(__DIR__ . "/../src/domain/value_object/vote.php");
require_once(__DIR__ . "/../src/domain/value_object/token.php");
require_once(__DIR__ . "/../src/domain/value_object/user.php");

use app\domain\entity\Option;
use app\domain\entity\Survey;
use app\domain\value_object\Token;
use app\domain\value_object\User;
use app\domain\value_object\Vote;
use app\infrastructure\database\DBConnection;
use app\infrastructure\repository\pdo\SurveyRepository;


$conn = DBConnection::getConnection();

$repo = new SurveyRepository($conn);

$repo->save(new Survey(6, "updated", "CCCCC", [(new Option(10, "override opt2", 0)), (new Option(null, "new opt2", 0))]));
