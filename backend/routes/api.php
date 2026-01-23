<?php

use App\Controllers\ProblemController;
use App\Controllers\RootCausesTreeController;
use App\Controllers\CrewController;
use App\Controllers\UserController;

/** @var \App\Core\Router $router */

// ========================= Crew Endpoint =========================

$router->get('/8d/crew/health', [CrewController::class, 'health']);
$router->get('/8d/crew', [CrewController::class, 'getAllCrews']);


// ========================= User Endpoint =========================

$router->get('/8d/user/health', [UserController::class, 'health']);
$router->get('/8d/user/{id}', [UserController::class, 'getUser']);
$router->get('/8d/me', [UserController::class, 'me']);


// ========================= Problems Endpoint =========================

$router->get('/8d/problems/health', [ProblemController::class, 'health']);
$router->get('/8d/problems', [ProblemController::class, 'getAllProblems']);
$router->get('/8d/problems/{id}', [ProblemController::class, 'getProblem']);
$router->post('/8d/problems', [ProblemController::class, 'store']);


// ======================= RootCausesTree Endpoint =======================

$router->get(
    '/8d/rootcauses/health',
    [RootCausesTreeController::class, 'health']
);

$router->get(
    '/8d/rootcauses/{id}', 
    [RootCausesTreeController::class, 'getRootCauseNode']
);

$router->get(
    '/8d/rootcauses/{problem_id}/tree', 
    [RootCausesTreeController::class, 'getTreeByProblemId']
);

$router->post(
    '/8d/rootcauses',
    [RootCausesTreeController::class, 'store']
);

$router->patch(
    '/8d/rootcauses/{id}/is_root_cause',
    [RootCausesTreeController::class, 'updateIsRootCause']
);