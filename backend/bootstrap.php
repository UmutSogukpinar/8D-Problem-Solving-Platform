<?php

declare(strict_types=1);

use App\Core\Container;
use App\Core\GlobalErrorHandler;

/*
|--------------------------------------------------------------------------
| Autoload (Composer)
|--------------------------------------------------------------------------
*/
require_once ROOT_DIR . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Global Error Handler
|--------------------------------------------------------------------------
*/
set_exception_handler([GlobalErrorHandler::class, 'handle']);

/*
|--------------------------------------------------------------------------
| Configurations and Utilities
|--------------------------------------------------------------------------
*/
require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/utils/logger.php';

require_once ROOT_DIR . '/database/migrate.php';

/*
|--------------------------------------------------------------------------
| Container
|--------------------------------------------------------------------------
*/
$container = new Container();

/*
|--------------------------------------------------------------------------
| 4. Bindings
|--------------------------------------------------------------------------
*/
$container->bind(PDO::class, fn (): PDO => getPdo());

return ($container);