<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/constants.php';
require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/utils/logger.php';

// Clear Log File
initLogger();

$pdo = getPdo();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
");

$executedMigrations = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

$migrationFiles = glob(ROOT_DIR . '/migrations/*.php');
sort($migrationFiles);

try
{
    $executedCount = 0;

    foreach ($migrationFiles as $file)
    {
        $fileName = basename($file);

        if (in_array($fileName, $executedMigrations)) {
            continue; 
        }

        $sql = null;
        require $file;

        if (!is_string($sql) || trim($sql) === '')
        {
            logMessage(WARNING, "Migration file $fileName did not define valid SQL. Skipping.");
            continue;
        }

        $pdo->exec($sql);
        
        $stmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->execute([$fileName]);

        logMessage(INFO, $fileName . ' executed successfully.');
        $executedCount++;
    }
    
    if ($executedCount > 0) 
    {
        logMessage(INFO, "$executedCount new migrations completed.");
    } 
    else 
    {
        logMessage(INFO, "Nothing to migrate.");
    }
}
catch (Throwable $e)
{
    logMessage(ERROR, 'Migration failed: ' . $e->getMessage());

    exit(1);
}