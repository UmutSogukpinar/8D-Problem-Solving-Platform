<?php

declare(strict_types=1);

use App\Core\Container;
use App\Core\Router;
use App\Core\Request;
use App\Core\GlobalErrorHandler;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/utils/logger.php';

// ====================================================================
// CORS HANDLING
// ====================================================================

if (isset($_SERVER['HTTP_ORIGIN']))
{
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) 
    {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH");
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
    {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }

    exit(0);
}


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

require_once dirname(__DIR__) . '/routes/api.php';

$router->dispatch();

