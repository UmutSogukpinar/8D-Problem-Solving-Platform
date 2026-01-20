<?php

declare(strict_types=1);

use PDO;

return function (PDO $pdo): void
{
    $targetCrewName = 'Alpha Crew';

    $stmtCrew = $pdo->prepare("SELECT id FROM crews WHERE name = :name");
    $stmtCrew->execute(['name' => $targetCrewName]);
    $crewId = $stmtCrew->fetchColumn();

    if ($crewId === false)
    {
        logMessage(WARNING, "Crew '{$targetCrewName}' not found, skipping user seeding for this crew.");
        return ; 
    }

    $crewId = (int)$crewId;

    $users = [
        [
            'crew_id' => $crewId,
            'name'    => 'Umut',
            'surname' => 'Sogukpinar',
            'email'   => 'umut@example.com',
            'password' => 'Password123!',
        ],
        [
            'crew_id' => $crewId,
            'name'    => 'Ada',
            'surname' => 'Lovelace',
            'email'   => 'ada@example.com',
            'password' => 'Password123!',
        ],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO users (crew_id, name, surname, email, password)
        VALUES (:crew_id, :name, :surname, :email, :password)
        ON DUPLICATE KEY UPDATE
            crew_id  = VALUES(crew_id),
            name     = VALUES(name),
            surname  = VALUES(surname),
            password = VALUES(password)
    ");

    foreach ($users as $u) 
    {
        $u['password'] = password_hash($u['password'], PASSWORD_BCRYPT);
        $stmt->execute($u);
    }

    logMessage(INFO, "Seed 2 executed successfuly!");
};
