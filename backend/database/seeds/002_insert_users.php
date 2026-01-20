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

    $users = [
        [
            'crew_id' => $crewId,
            'name' => 'Umut',
            'surname' => 'Sogukpinar',
            'email' => 'umut@example.com',
            'password' => password_hash('Password123!', PASSWORD_BCRYPT),
        ],
        [
            'crew_id' => $crewId,
            'name' => 'Ada',
            'surname' => 'Lovelace',
            'email' => 'ada@example.com',
            'password' => password_hash('Password123!', PASSWORD_BCRYPT),
        ],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO users (crew_id, name, surname, email, password)
        VALUES (:crew_id, :name, :surname, :email, :password)
        ON DUPLICATE KEY UPDATE
            crew_id = VALUES(crew_id),
            name = VALUES(name),
            surname = VALUES(surname),
            password = VALUES(password)
    ");

    foreach ($users as $u) {
        $stmt->execute($u);
    }
};
