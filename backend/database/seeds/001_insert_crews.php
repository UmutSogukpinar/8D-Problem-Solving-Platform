<?php

declare(strict_types=1);

return function (PDO $pdo): void 
{
    $crews = [
        'Alpha Crew',
        'Beta Crew',
    ];

    $stmt = $pdo->prepare("
        INSERT INTO crews (name)
        SELECT :val
        WHERE NOT EXISTS (
            SELECT 1 FROM crews WHERE name = :check
        )
    ");

    foreach ($crews as $name)
    {
        $stmt->execute([
            'val'   => $name,
            'check' => $name
        ]);
    }

    logMessage(INFO, "Seed 1 executed successfuly!");
};
