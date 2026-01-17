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
