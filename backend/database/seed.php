<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/utils/logger.php';


$pdo = getPdo();

$seedFiles = glob(__DIR__ . '/seeds/*.php');
sort($seedFiles);

$pdo->beginTransaction();

try
{
    foreach ($seedFiles as $file)
    {
        $seed = require $file;

        if (!is_callable($seed))
        {
            throw new RuntimeException("Seed file must return a callable: {$file}");
        }

        $seed($pdo);
    }
    $pdo->commit();
    logMessage(INFO, "Seeding completed");
}
catch (Throwable $e)
{
    $pdo->rollBack();
    logMessage(ERROR, "Seeding failed: " . $e->getMessage());
    exit(1);
}
