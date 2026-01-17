<?php

declare(strict_types=1);

require_once ROOT_DIR . '/config/constants.php';

/**
 * Creates and returns a PDO instance for database access.
 *
 * Uses database connection parameters defined in config constants
 * and enables exception-based error handling.
 *
 * @return PDO Database connection instance
 *
 * @throws PDOException If the connection fails
 */
function getPdo(): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );

    return (new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    )
    );
}
