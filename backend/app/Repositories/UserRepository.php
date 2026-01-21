<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final Class UserRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    /**
     * Finds a single user by its identifier.
     *
     * Executes a database query to retrieve a user record along with
     * its associated crew information. If no matching user is found,
     * null is returned.
     *
     * @param int $id User identifier
     *
     * @return array|null Associative array containing user and crew data,
     *                    or null if the user does not exist
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "
            SELECT 
                u.id    AS user_id,
                u.name  AS user_name,
                u.email AS user_email,

                c.id    AS crew_id,
                c.name  AS crew_name

            FROM users u
            INNER JOIN crews c ON u.crew_id = c.id
            WHERE u.id = :id
            "
        );

        $stmt->execute(['id' => $id]);

        return ($stmt->fetch() ?: null);
    }
}