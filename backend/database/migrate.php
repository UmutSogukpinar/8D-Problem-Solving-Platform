<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/constants.php';
require_once ROOT_DIR . '/database/connection.php';
require_once ROOT_DIR . '/logger.php';

// Get PDO connection
$pdo = getPdo();

// Get all migration files and sort them to ensure correct order
$migrationFiles = glob(ROOT_DIR . '/migrations/*.php');
sort($migrationFiles);

foreach ($migrationFiles as $file) 
{
    try 
    {
        require $file;
        $pdo->exec($sql);
        logMessage(INFO, basename($file) . ' executed');
    } 
    catch (Throwable $e) 
    {
        logMessage(ERROR, basename($file) . ' failed: ' . $e->getMessage());
        exit(1);
    }
}

logMessage(INFO, 'All migrations completed');
