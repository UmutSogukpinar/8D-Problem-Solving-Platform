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
require_once ROOT_DIR . '/database/migrate.php';

/*
|--------------------------------------------------------------------------
| Application layers
|--------------------------------------------------------------------------
*/
require_once ROOT_DIR . '/app/repositories/ProblemRepository.php';
require_once ROOT_DIR . '/app/services/ProblemService.php';
require_once ROOT_DIR . '/app/controllers/ProblemController.php';

require_once ROOT_DIR . '/app/repositories/RootCausesTreeRepository.php';
require_once ROOT_DIR . '/app/services/RootCausesTreeService.php';
require_once ROOT_DIR . '/app/controllers/RootCausesTreeController.php';

require_once ROOT_DIR . '/app/controllers/BaseControoler.php';

