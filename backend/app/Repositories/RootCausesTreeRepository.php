<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;

final class RootCausesTreeRepository
{
	public function __construct(private PDO $pdo) {}

	/**
	 * Persists a new root cause node in the database.
	 *
	 * Creates a new record in `root_causes_tree`. If $parentId is null, the node
	 * is created as a root-level node. Otherwise, it becomes a child of the given
	 * parent node.
	 *
	 * @param int      $problemId Related problem identifier.
	 * @param int|null $parentId  Parent node identifier (null for root nodes).
	 * @param string   $desc      Root cause description.
	 *
	 * @return int Newly created node ID.
	 *
	 * @throws PDOException If the insert operation fails.
	 */
	public function create(int $problemId, ?int $parentId, string $desc): int
	{
		$stmt = $this->pdo->prepare(
			"
			INSERT INTO root_causes_tree (problem_id, parent_id, description)
			VALUES (:problem_id, :parent_id, :description)
			"
		);

		$stmt->execute([
			'problem_id' => $problemId,
			'parent_id' => $parentId,
			'description' => $desc,
		]);

		return ((int)$this->pdo->lastInsertId());
	}

	/**
	 * Retrieves a single root cause node by its unique identifier.
	 *
	 * Returns the node data along with the related problem context:
	 *  - problem fields (id, title, description, created_at)
	 *  - creator fields (id, name)
	 *  - crew fields (id, name)
	 *
	 * This method returns null if the node does not exist.
	 *
	 * @param int $id Root cause node identifier.
	 *
	 * @return array|null Root cause node + problem context as an associative array,
	 *                    or null if not found.
	 *
	 * @throws PDOException If the query execution fails.
	 */
	public function findById(int $id): ?array
	{
		$stmt = $this->pdo->prepare(
			"
			SELECT
				rct.id          AS node_id,
				rct.problem_id  AS node_problem_id,
				rct.parent_id   AS node_parent_id,
				rct.description AS node_description,
				rct.created_at  AS node_created_at,

				p.id            AS problem_id,
				p.title         AS problem_title,
				p.description   AS problem_description,
				p.created_at    AS problem_created_at,

				u.id            AS created_by_id,
				u.name          AS created_by_name,

				c.id            AS crew_id,
				c.name          AS crew_name

			FROM root_causes_tree rct
			INNER JOIN problems p ON p.id = rct.problem_id
			INNER JOIN users u    ON u.id = p.created_by
			INNER JOIN crews c    ON c.id = p.crew_id
			WHERE rct.id = :id
			LIMIT 1
			"
		);

		$stmt->execute(['id' => $id]);

		return ($stmt->fetch(PDO::FETCH_ASSOC) ?: null);
	}

	/**
	 * Retrieves root cause nodes for a given problem filtered by parent node.
	 *
	 * - If $parentId is null, returns root-level nodes (parent_id IS NULL).
	 * - If $parentId is provided, returns direct children of that parent.
	 *
	 * Each record includes the node data + related problem context (same shape as {@see findById()}).
	 *
	 * @param int      $problemId Related problem identifier.
	 * @param int|null $parentId  Parent node identifier (null for root nodes).
	 *
	 * @return array List of associative arrays. Empty array if no matches.
	 *
	 * @throws PDOException If the query execution fails.
	 */
	public function findByParent(int $problemId, ?int $parentId): array
	{
		$stmt = $this->pdo->prepare(
			"
			SELECT
				rct.id          AS node_id,
				rct.problem_id  AS node_problem_id,
				rct.parent_id   AS node_parent_id,
				rct.description AS node_description,
				rct.created_at  AS node_created_at,

				p.id            AS problem_id,
				p.title         AS problem_title,
				p.description   AS problem_description,
				p.created_at    AS problem_created_at,

				u.id            AS created_by_id,
				u.name          AS created_by_name,

				c.id            AS crew_id,
				c.name          AS crew_name

			FROM root_causes_tree rct
			INNER JOIN problems p ON p.id = rct.problem_id
			INNER JOIN users u    ON u.id = p.created_by
			INNER JOIN crews c    ON c.id = p.crew_id
			WHERE rct.problem_id = :problem_id
			  AND (
					(:parent_id IS NULL AND rct.parent_id IS NULL)
				 OR (:parent_id IS NOT NULL AND rct.parent_id = :parent_id)
			  )
			ORDER BY rct.created_at ASC, rct.id ASC
			"
		);

		$stmt->execute([
			'problem_id' => $problemId,
			'parent_id' => $parentId,
		]);

		return ($stmt->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * Retrieves the full root cause tree (flat list) for a given problem.
	 *
	 * Returns all nodes for the given problem as a flat list (not nested).
	 * Each record includes node data + related problem context (same shape as {@see findById()}).
	 *
	 * @param int $problemId Related problem identifier.
	 *
	 * @return array List of associative arrays. Empty array if the problem has no nodes.
	 *
	 * @throws PDOException If the query execution fails.
	 */
	public function findTreeByProblemId(int $problemId): ?array
    {
        $stmt = $this->pdo->prepare(
"
            SELECT
                p.id            AS problem_id,
                p.title         AS problem_title,
                p.description   AS problem_description,
                p.created_at    AS problem_created_at,

                u.id            AS created_by_id,
                u.name          AS created_by_name,
                
                c.id            AS crew_id,
                c.name          AS crew_name,

                rct.id          AS id, 
                rct.parent_id   AS parent_id,
                rct.description AS description,
                rct.created_at  AS created_at,

                rct.author_id   AS author_id,
                ua.name         AS author_name

            FROM problems p

            LEFT JOIN root_causes_tree rct ON rct.problem_id = p.id
            
            INNER JOIN users u    ON u.id = p.created_by
            
            LEFT JOIN users ua    ON ua.id = rct.author_id

            INNER JOIN crews c    ON c.id = p.crew_id
            
            WHERE p.id = :problem_id
            
            ORDER BY rct.created_at ASC, rct.id ASC
            "
        );

        $stmt->execute(['problem_id' => $problemId]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result))
		{
            return (null);
        }
		
        return ($result);
    }
}
