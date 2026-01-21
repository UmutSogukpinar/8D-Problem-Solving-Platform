<?php

declare (strict_types=1);

namespace App\Repositories;

use PDO;

final class CrewRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Retrieves all crews from the database.
     *
     * Fetches every crew record with its identifier, name, and role.
     * The result is returned as an associative array.
     *
     * @return array<int, array{
     *     id: int,
     *     name: string,
     * }>
     *
     * @throws PDOException If the query execution fails
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare(
            '
                SELECT id, name
                FROM crews
            '
        );

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}