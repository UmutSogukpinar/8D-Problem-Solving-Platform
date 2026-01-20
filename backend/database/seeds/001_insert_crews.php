<?php

declare(strict_types=1);

use PDO;

return function (PDO $pdo): void {
    $crews = [
        'Alpha Crew',
        'Beta Crew',
    ];

    $stmt = $pdo->prepare("
        INSERT INTO crews (name)
        SELECT :name
        WHERE NOT EXISTS (
            SELECT 1 FROM crews WHERE name = :name
        )
    ");

    foreach ($crews as $name) {
        $stmt->execute(['name' => $name]);
    }
};
