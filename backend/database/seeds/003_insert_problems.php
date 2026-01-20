<?php

declare(strict_types=1);

use PDO;

return function (PDO $pdo): void {
    $crewId = (int)$pdo->query(
        "
        SELECT id 
        FROM crews 
        WHERE name='Alpha Crew'
        "
    )->fetchColumn();

    $createdBy = (int)$pdo->query(
        "
        SELECT id 
        FROM users
        WHERE email='umut@example.com'
        "
    )->fetchColumn();

    $problems = [
        [
            'title' => 'Login fails intermittently',
            'description' => 'Users sometimes cannot log in; returns 500 sporadically.',
        ],
        [
            'title' => 'Slow API response',
            'description' => 'GET /problems takes > 3s under load.',
        ],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO problems (title, description, created_by, crew_id)
        SELECT :title, :description, :created_by, :crew_id
        WHERE NOT EXISTS (
            SELECT 1 FROM problems
            WHERE crew_id = :crew_id AND title = :title
        )
    ");

    foreach ($problems as $p) {
        $stmt->execute([
            'title' => $p['title'],
            'description' => $p['description'],
            'created_by' => $createdBy,
            'crew_id' => $crewId,
        ]);
    }
};
