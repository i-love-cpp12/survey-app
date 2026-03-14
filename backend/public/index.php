<?php
require_once(__DIR__ . "/../src/interface/router/router.php");

require_once(__DIR__ . "/../src/interface/controler/survey_vote_controler.php");
require_once(__DIR__ . "/../src/interface/controler/survey_controler.php");

require_once(__DIR__ . "/../src/application/service/survey_vote_service.php");
require_once(__DIR__ . "/../src/application/service/survey_get_by_code_service.php");
require_once(__DIR__ . "/../src/application/service/survey_get_all_service.php");

require_once(__DIR__ . "/../src/infrastructure/repository/dummy/survey_repository.php");
require_once(__DIR__ . "/../src/infrastructure/repository/dummy/survey_vote_repository.php");

require_once(__DIR__ . "/../src/infrastructure/http/request.php");

use app\application\service\SurveyGetAllService;
use app\application\service\SurveyGetByCodeService;
use app\application\service\SurveyGetResultsService;
use app\application\service\SurveyVoteService;
use app\infrastructure\http\Request;
use app\interface\router\Router;
use app\infrastructure\repository\dummy\DummySurveyVoteRepository;
use app\infrastructure\repository\dummy\DummySurveyRepository;
use app\interface\controler\SurveyControler;
use app\interface\controler\SurveyVoteControler;

$router = new Router();
$voteRepository = new DummySurveyVoteRepository();
$surveyRepository = new DummySurveyRepository();

$voteService = new SurveyVoteService($surveyRepository, $voteRepository);
$getByCodeService = new SurveyGetByCodeService($surveyRepository);
$getAllService = new SurveyGetAllService($surveyRepository);
$getResultsService = new SurveyGetResultsService($surveyRepository);

$voteControler = new SurveyVoteControler($voteService);
$surveyControler = new SurveyControler($getByCodeService, $getAllService, $getResultsService);

$router->get("backend/survey/{code}", [$surveyControler, 'getByCode']);
$router->get("backend/survey", [$surveyControler, 'getAll']);
$router->get("backend/survey/{code}/results", [$surveyControler, 'getResults']);
$router->post("backend/survey/{code}/vote", [$voteControler, 'vote']);

$request = new Request();
$router->exectute($request->method, $request->uri);