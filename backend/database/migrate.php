<?php

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/utils/logger.php';

initLogger();

$pdo = getPdo();

$migrationFiles = glob(ROOT_DIR . '/migrations/*.php');
sort($migrationFiles);

$pdo->beginTransaction();

try
{
    foreach ($migrationFiles as $file)
    {
        $sql = null;
        require $file;

        if (!is_string($sql) || trim($sql) === '')
        {
            throw new RuntimeException(
                'Migration did not define $sql as a non-empty string'
            );
        }

        $pdo->exec($sql);
        logMessage(INFO, basename($file) . ' executed');
    }

    logMessage(INFO, 'All migrations completed');
}
catch (Throwable $e)
{
    if ($pdo->inTransaction()) 
    {
        $pdo->rollBack();
    }
    logMessage(ERROR, 'Migration failed: ' . $e->getMessage());
    exit(1);
}
