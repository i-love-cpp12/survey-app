<?php
require_once(__DIR__ . "/../src/interface/router/router.php");


use app\interface\router\Router;

$router = new Router();

$router->get("/api/survey/{code}/vote", function ($arg) {echo "working $arg"; exit();});

$router->exectute("POST", "/api/survey/ASDJ/vote");