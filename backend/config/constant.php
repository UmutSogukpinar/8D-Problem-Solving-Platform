<?php
declare(strict_types=1);

// Root directory of the project
define('ROOT_DIR', dirname(__DIR__)); 

// Upload directory
define('UPLOAD_DIR', ROOT_DIR . '/public/uploads');

// ====== Log message prefixes ======

define('INFO', '[INFO]: ');
define('ERROR', '[ERROR]: ');
define('SUCCESS', '[SUCCESS]: ');
define('WARNING', '[WARNING]: ');

// ====== Database configuration ======

define('DB_HOST', 'localhost');
define('DB_NAME', 'test_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');


// ====== Logging configuration ======

define('LOG_DIR', ROOT_DIR . '/logs');
define('APP_LOG', LOG_DIR . '/app.log');
// TODO: make the log level dynamic based on environment and user preference by docker cmd
define('LOG_LEVEL', INFO); // INFO | WARNING | ERROR


// ====== HTTP status codes ======

define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_NO_CONTENT', 204);

define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_CONFLICT', 409);
define('HTTP_UNPROCESSABLE_ENTITY', 422);

define('HTTP_INTERNAL_SERVER_ERROR', 500);
define('HTTP_NOT_IMPLEMENTED', 501);
define('HTTP_SERVICE_UNAVAILABLE', 503);