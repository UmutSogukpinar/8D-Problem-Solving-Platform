<?php

final class RootCausesTreeRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Persists a new root cause node in the database.
     *
     * @param int      $problemId The identifier of the related problem.
     * @param int|null $parentId  Parent root cause identifier, or null for a root node.
     * @param string   $desc      Description of the root cause.
     *
     * @return int ID of the newly created root cause record.
     *
     * @throws PDOException If the insert operation fails.
     */
    public function create(int $problemId, ?int $parentId, string $desc): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO root_causes_tree (problem_id, parent_id, description)
            VALUES (:problem_id, :parent_id, :d)"
        );

        $stmt->execute([
            'problem_id' => $problemId,
            'parent_id' => $parentId,
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
            "SELECT * 
            FROM root_causes_tree 
            WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        return ($stmt->fetch() ?: null);
    }

    /**
     * Retrieves root cause nodes by problem and parent identifier.
     *
     * @param int      $problemId The identifier of the related problem.
     * @param int|null $parentId  Parent root cause identifier, or null for root nodes.
     *
     * @return array List of root cause records (empty array if no matches are found).
     *
     * @throws PDOException If the query execution fails.
     */
    public function findByParent(int $problemId, ?int $parentId): array
    {
        if ($parentId === null)
        {
            $stmt = $this->pdo->prepare(
                "SELECT *
                FROM root_causes_tree
                WHERE problem_id = :problem_id AND parent_id IS NULL"
            );
            $stmt->execute(['problem_id' => $problemId]);
        }
        else
        {
            $stmt = $this->pdo->prepare(
                "SELECT *
                FROM root_causes_tree
                WHERE problem_id = :problem_id AND parent_id = :parent_id"
            );
            $stmt->execute([
                'problem_id' => $problemId,
                'parent_id' => $parentId
            ]);
        }

        return ($stmt->fetchAll());
    }

    /**
     * Retrieves all root cause nodes associated with a given problem.
     *
     * @param int $problemId The identifier of the related problem.
     *
     * @return array List of root cause records.
     *
     * @throws PDOException If the query execution fails.
     */
    public function findTreeByProblemId(int $problemId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
            FROM root_causes_tree
            WHERE problem_id = :problem_id"
        );

        $stmt->execute(['problem_id' => $problemId]);

        return ($stmt->fetchAll());
    }
}
