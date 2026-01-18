<?php

final class ProblemRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Persists a new problem record in the database.
     *
     * Inserts a problem with the given title and description and returns
     * the auto-generated primary key.
     *
     * @param string $title Problem title
     * @param string $desc  Problem description
     *
     * @return int ID of the newly created problem
     *
     * @throws PDOException If the insert operation fails
     */
    public function create(string $title, string $desc): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO problems (title, description)
             VALUES (:t, :d)"
        );

        $stmt->execute([
            't' => $title,
            'd' => $desc
        ]);

        return ((int) $this->pdo->lastInsertId());
    }


    /**
     * Retrieves a problem by its unique identifier.
     *
     * Executes a SELECT query and returns the problem data as an
     * associative array. If no record is found, null is returned.
     *
     * @param int $id Problem identifier
     *
     * @return array|null Problem data or null if not found
     *
     * @throws PDOException If the query execution fails
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM problems WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        return ($stmt->fetch() ?: null);
    }

    /**
     * Retrieves problems by their parent identifier.
     *
     * Executes a SELECT query and returns the matching problems
     * as an associative array. If the given parent ID is null,
     * only root-level problems are returned.
     * If no records are found, null is returned.
     *
     * @param int|null $parentId Parent problem identifier or null for root problems
     *
     * @return array|null List of problem records or null if no matches are found
     *
     * @throws PDOException If the query execution fails
     */
    public function findByParent(?int $parentId): ?array
    {
        if ($parentId === null)
        {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM problems WHERE parentId IS NULL"
            );
            $stmt->execute();
        }
        else
        {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM problems WHERE parentId = :parentId"
            );
            $stmt->execute(['parentId' => $parentId]);
        }

        return ($stmt->fetchAll() ?: null);
    }
}
