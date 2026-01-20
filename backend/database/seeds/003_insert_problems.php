<?php

declare(strict_types=1);

return function (PDO $pdo): void
{
    $targetCrewName = 'Alpha Crew';
    $creatorEmail = 'umut@example.com';

    $stmtCrew = $pdo->prepare("SELECT id FROM crews WHERE name = :name");
    $stmtCrew->execute(['name' => $targetCrewName]);
    $crewId = $stmtCrew->fetchColumn();

    if ($crewId === false)
    {
        logMessage(
            WARNING,
            "Crew '{$targetCrewName}' not found, skipping problem seeding."
        );
        return ;
    }

    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmtUser->execute(['email' => $creatorEmail]);
    $createdBy = $stmtUser->fetchColumn();

    if ($createdBy === false)
    {
        logMessage(
            WARNING,
            "User '{$creatorEmail}' not found. Problem seeding skipped."
        );
        return ;
    }

    $crewId = (int)$crewId;
    $createdBy = (int)$createdBy;

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
            WHERE crew_id = :check_crew_id AND title = :check_title
        )
    ");

    foreach ($problems as $p)
    {
        $stmt->execute([
            'title'         => $p['title'],
            'description'   => $p['description'],
            'created_by'    => $createdBy,
            'crew_id'       => $crewId,
            'check_crew_id' => $crewId,
            'check_title'   => $p['title']
        ]);
    }

    logMessage(INFO, "Seed 3 executed successfuly!");
};