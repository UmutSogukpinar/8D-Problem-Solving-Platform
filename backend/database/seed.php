<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$pdo = getPdo();

$seedFiles = glob(__DIR__ . '/seeds/*.php');
sort($seedFiles);

$pdo->beginTransaction();

try
{
    foreach ($seedFiles as $file)
    {
        $seed = null;
        require $file;

        if (!is_callable($seed))
        {
            throw new RuntimeException("Seed file must return a callable: {$file}");
        }

        $seed($pdo);
    }

    $pdo->commit();
    logMessage(DEBUG, "Seeding completed");
}
catch (Throwable $e)
{
    $pdo->rollBack();
    logMessage(ERROR, "Seeding failed: " . $e->getMessage());
    exit(1);
}
