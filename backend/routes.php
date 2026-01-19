<?php

declare(strict_types=1);

use App\Controllers\ProblemController;
use App\Controllers\RootCausesTreeController;

/** @var App\Http\Router $router */

$router->add('GET', '/problems', [ProblemController::class, 'index']);
// $router->add('POST', '/problems', [ProblemController::class, 'store']);
// $router->add('GET', '/root-causes', [RootCausesTreeController::class, 'index']);