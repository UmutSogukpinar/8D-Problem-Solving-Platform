<?php

declare(strict_types=1);

require_once ROOT_DIR . '/config/constants.php';

/**
 * Returns a shared PDO instance for database access.
 *
 * Database connection parameters are read from configuration constants.
 * 
 * The connection is configured to:
 *  - throw exceptions on errors
 *  - return associative arrays by default
 *  - use real prepared statements
 *
 * @return PDO Shared PDO connection instance
 *
 * @throws PDOException If the database connection cannot be established
 */
function getPdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO)
    {
        return ($pdo);
    }

    $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    return ($pdo);
}
