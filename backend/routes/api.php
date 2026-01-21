<?php

use App\Controllers\ProblemController;
use App\Controllers\RootCausesTreeController;
use App\Controllers\CrewController;

/** @var \App\Core\Router $router */

// ========================= Problems Endpoint =========================
$router->get('/8d/crew/health', [CrewController::class, 'health']);
$router->get('/8d/crew', [CrewController::class, 'getAllCrews']);



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