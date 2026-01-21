<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProblemRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Persists a new problem record in the database.
     *
     * Inserts a problem with the given title and description and returns
     * the auto-generated primary key.
     *
     * @param string $title     Problem title
     * @param string $desc      Problem description
     * @param int    $crewId    The id that belongs to the crew
     *                           which interact with the problem
     * @param int   $userId     The id that belongs to the person
     *                           who insert problem
     *
     * @return int ID of the newly created problem
     *
     * @throws PDOException If the insert operation fails
     */
    public function create(string $title, string $desc, int $crewId, int $userId): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO problems (title, description, crew_id, created_by, created_at)
                VALUES (:t, :d, :c, :u, NOW())"
        );

        $stmt->execute([
            't' => $title,
            'd' => $desc,
            'c' => $crewId,
            'u' => $userId
        ]);

        return ((int) $this->pdo->lastInsertId());
    }

    /**
     * Retrieves a problem by its unique identifier.
     *
     * Fetches a single problem record along with its related creator
     * and crew information. The returned structure is consistent with
     * the data shape produced by {@see findAll()}.
     *
     * @param int $id Problem identifier
     *
     * @return array|null Problem data as an associative array, or null if not found
     *
     * @throws PDOException If the query execution fails
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "
            SELECT
                p.id,
                p.title,
                p.description,
                p.created_at,

                u.id   AS created_by_id,
                u.name AS created_by_name,

                c.id   AS crew_id,
                c.name AS crew_name

            FROM problems p
            INNER JOIN users u ON u.id = p.created_by
            INNER JOIN crews c ON c.id = p.crew_id
            WHERE p.id = :id
            LIMIT 1

		    "
        );

        $stmt->execute(['id' => $id]);

        return ($stmt->fetch(\PDO::FETCH_ASSOC) ?: null);
    }


    /**
     * Retrieves all records from the problems table.
     *
     * @return array List of problems as associative arrays.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare(
            "
            SELECT
                p.id,
                p.title,
                p.description,
                p.created_at,

                u.id   AS created_by_id,
                u.name AS created_by_name,

                c.id   AS crew_id,
                c.name AS crew_name

            FROM problems p
            INNER JOIN users u ON u.id = p.created_by
            INNER JOIN crews c ON c.id = p.crew_id
            ORDER BY p.created_at DESC
            "
        );

        $stmt->execute();

        return ($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }
}
