<?php
require_once(__DIR__ . "/../src/interface/router/router.php");
require_once(__DIR__ . "/../src/interface/controler/survey_vote_controler.php");
require_once(__DIR__ . "/../src/infrastructure/repository/survey_vote_repository.php");
require_once(__DIR__ . "/../src/infrastructure/repository/survey_repository.php");
require_once(__DIR__ . "/../src/application/service/survey_vote_service.php");

use app\application\service\SurveyVoteService;
use app\interface\router\Router;
use app\infrastructure\repository\DummySurveyVoteRepository;
use app\infrastructure\repository\DummySurveyRepository;
use app\interface\controler\SurveyVoteControler;

$router = new Router();
$voteRepository = new DummySurveyVoteRepository();
$surveyRepository = new DummySurveyRepository();

$voteService = new SurveyVoteService($surveyRepository, $voteRepository);
$voteControler = new SurveyVoteControler($voteService);


$router->get("survey/{code}/vote", function ($arg) {echo "working $arg"; exit();});

$router->exectute($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);