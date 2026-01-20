<?php

use App\Controllers\ProblemController;
use App\Controllers\RootCausesTreeController;

/** @var \App\Core\Router $router */


$router->get('/problems/health', [ProblemController::class, 'health']);
$router->get('/problems/{id}', [ProblemController::class, 'getProblem']);
$router->post('/problems', [ProblemController::class, 'store']);


$router->get('/rootcauses/health', [RootCausesTreeController::class, 'health']);
$router->get('/rootcauses/{id}', [RootCausesTreeController::class, 'getRootCauseNode']);
$router->get('/problems/{problem_id}/tree', [RootCausesTreeController::class, 'getTreeByProblemId']);