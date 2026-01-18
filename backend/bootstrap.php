<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Core configuration
|--------------------------------------------------------------------------
*/
require_once ROOT_DIR . '/config/constant.php';
require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/utils/logger.php';

/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/
require_once ROOT_DIR . '/database/getPdo.php';

/*
|--------------------------------------------------------------------------
| Application layers
|--------------------------------------------------------------------------
*/
require_once ROOT_DIR . '/repositories/ProblemRepository.php';
require_once ROOT_DIR . '/services/ProblemService.php';
require_once ROOT_DIR . '/controllers/ProblemController.php';
