<?php

declare(strict_types=1);

use App\Core\Router;

define('ROOT_DIR', dirname(__DIR__));

$container = require ROOT_DIR . '/bootstrap.php';

$router = new Router($container);

require ROOT_DIR . '/routes.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);