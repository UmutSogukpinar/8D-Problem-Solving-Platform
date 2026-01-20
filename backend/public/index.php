<?php

declare(strict_types=1);

use App\Core\Container;
use App\Core\Router;
use App\Core\Request;
use App\Core\GlobalErrorHandler;
use App\Controllers\ProblemController;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/utils/logger.php';

logMessage(DEBUG, "Server starts!");

// Setup error handling
set_exception_handler([GlobalErrorHandler::class, 'handle']);

// Start the DI Container
$container = new Container();

// --- Binding Dependencies ---

$container->bind(
    Request::class,
    function () {
        return (new Request());
    }
);

// Database Connection (PDO)
$container->bind(
    PDO::class,
    function () {
        require_once __DIR__ . '/../config/database.php';
        return (getPdo());
    }
);

// Start the Router
$router = new Router($container);

// ======== Define Routes ========
$router->get('/problems/health', [ProblemController::class, 'health']);

$router->dispatch();
