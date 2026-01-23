<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;

final class RootCausesTreeRepository
{
	public function __construct(private PDO $pdo) {}

	/**
	 * Toggles the `is_root_cause` flag for a specific root cause node.
	 *
	 * Updates the `is_root_cause` field to its opposite value (true to false, or false to true)
	 * for the node with the given ID. If the update is successful, the updated node data
	 * is retrieved and returned.
	 *
	 * @param int $id The unique identifier of the root cause node to update.
	 *
	 * @return array|null The updated node data as an associative array, or null if no rows were updated.
	 *
	 * @throws \PDOException If the query execution fails.
	 */
	public function updateIsRootCause(int $id): ?array
	{
		$sql = "
				UPDATE root_causes_tree 
				SET is_root_cause = NOT is_root_cause 
				WHERE id = :id
				";
				
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(['id' => $id]);

		if ($stmt->rowCount() > 0)
			{
			$stmt = $this->pdo->prepare("SELECT * FROM root_causes_tree WHERE id = :id");
			$stmt->execute(['id' => $id]);
			
			return ($stmt->fetch(PDO::FETCH_ASSOC));
		}

		return (null);
	}

	/**
	 * Inserts a new root cause tree node.
	 *
	 * Persists a node linked to a specific problem. If $parentId is null, the node is a root-level node.
	 *
	 * @param int      $problemId Problem ID the node belongs to.
	 * @param int|null $parentId  Parent node ID; null for root-level nodes.
	 * @param string   $desc      Node description text.
	 * @param int      $authorId  User ID of the node author/creator.
	 *
	 * @return int Newly created node ID (AUTO_INCREMENT id).
	 *
	 * @throws PDOException If prepare/execute fails.
	 */
	public function create(
		int $problemId,
		?int $parentId,
		string $desc,
		int $authorId
	): int
	{
		$stmt = $this->pdo->prepare(
			"
			INSERT INTO root_causes_tree (problem_id, parent_id, description, author_id)
			VALUES (:problem_id, :parent_id, :description, :author_id)
			"
		);

		$stmt->execute(
			[
				'problem_id' => $problemId,
				'parent_id' => $parentId,
				'description' => $desc,
				'author_id' => $authorId
			]
		);

		return ((int)$this->pdo->lastInsertId());
	}

	/**
	 * Fetches a single node by its ID with its related problem context.
	 *
	 * Returns a single flat associative row using the `baseNodeSelect()` column aliases.
	 *
	 * @param int $id Node ID.
	 *
	 * @return array{
	 *     node_id: int,
	 *     node_problem_id: int,
	 *     node_parent_id: int|null,
	 *     node_description: string,
	 *     node_created_at: string,
	 *     is_root_cause: int|string|null,
	 *     author_id: int|null,
	 *     author_name: string|null,
	 *     problem_id: int,
	 *     problem_title: string,
	 *     problem_description: string,
	 *     problem_created_at: string,
	 *     created_by_id: int,
	 *     created_by_name: string,
	 *     crew_id: int,
	 *     crew_name: string
	 * }|null Flat row if found, otherwise null.
	 *
	 * @throws PDOException If prepare/execute fails.
	 */
	public function findById(int $id): ?array
	{
		$stmt = $this->pdo->prepare(
			$this->baseNodeSelect() .
			$this->baseNodeJoins() .
			"
			WHERE rct.id = :id
			LIMIT 1
			"
		);

		$stmt->execute(['id' => $id]);

		return ($stmt->fetch(PDO::FETCH_ASSOC) ?: null);
	}

	/**
	 * Fetches direct children of a parent node for a problem (or root-level nodes).
	 *
	 * Behavior:
	 * - If $parentId is null: returns nodes where rct.parent_id IS NULL (root-level).
	 * - If $parentId is not null: returns nodes where rct.parent_id = $parentId (direct children only).
	 *
	 * Each row is a flat associative array using the `baseNodeSelect()` column aliases.
	 *
	 * @param int      $problemId Problem ID to filter by.
	 * @param int|null $parentId  Parent node ID to filter by; null for root-level nodes.
	 *
	 * @return array<int, array{
	 *     node_id: int,
	 *     node_problem_id: int,
	 *     node_parent_id: int|null,
	 *     node_description: string,
	 *     node_created_at: string,
	 *     is_root_cause: int|string|null,
	 *     author_id: int|null,
	 *     author_name: string|null,
	 *     problem_id: int,
	 *     problem_title: string,
	 *     problem_description: string,
	 *     problem_created_at: string,
	 *     created_by_id: int,
	 *     created_by_name: string,
	 *     crew_id: int,
	 *     crew_name: string
	 * }> List of matching nodes; empty array if none found.
	 *
	 * @throws PDOException If prepare/execute fails.
	 */
	public function findByParent(int $problemId, ?int $parentId): array
	{
		$stmt = $this->pdo->prepare(
			$this->baseNodeSelect() .
			$this->baseNodeJoins() .
			"
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
			'parent_id'  => $parentId,
		]);

		return ($stmt->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * Fetches the full root cause tree for a problem as a flat list (not nested).
	 *
	 * Important:
	 * - This method uses a different SELECT/alias set than `baseNodeSelect()`.
	 * - Because it LEFT JOINs `root_causes_tree`, a problem can exist with zero nodes.
	 *   In that case, the result may contain a single row where node columns are null/empty.
	 *
	 * @param int $problemId Problem ID.
	 *
	 * @return array<int, array{
	 *     problem_id: int,
	 *     problem_title: string,
	 *     problem_description: string,
	 *     problem_created_at: string,
	 *     created_by_id: int,
	 *     created_by_name: string,
	 *     is_root_cause: int|string|null,
	 *     crew_id: int,
	 *     crew_name: string,
	 *     id: int|null,
	 *     parent_id: int|null,
	 *     description: string|null,
	 *     created_at: string|null,
	 *     author_id: int|null,
	 *     author_name: string|null
	 * }>|null List of rows if the problem exists; null if no rows are returned.
	 *
	 * @throws PDOException If prepare/execute fails.
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

				rct.id            AS id,
				rct.parent_id     AS parent_id,
				rct.description   AS description,
				rct.created_at    AS created_at,
				rct.is_root_cause AS is_root_cause,

				rct.author_id     AS author_id,
				ua.name           AS author_name

			FROM problems p

			LEFT JOIN root_causes_tree rct ON rct.problem_id = p.id

			INNER JOIN users u    ON u.id = p.created_by
			LEFT JOIN users ua   ON ua.id = rct.author_id
			INNER JOIN crews c   ON c.id = p.crew_id

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

	/**
	 * Builds the SELECT clause (with aliases) used by node-context queries.
	 *
	 * This is shared by `findById()` and `findByParent()` to guarantee the same output shape.
	 *
	 * @return string SQL SELECT clause including column aliases.
	 */
	private function baseNodeSelect(): string
	{
		return (
			"
			SELECT
				rct.id            AS id,
				rct.parent_id     AS parent_id,
				rct.description   AS description,
				rct.created_at    AS created_at,

				rct.is_root_cause AS is_root_cause,

				rct.author_id     AS author_id,
				ua.name           AS author_name,

				p.id              AS problem_id,
				p.title           AS problem_title,
				p.description     AS problem_description,
				p.created_at      AS problem_created_at,

				u.id              AS created_by_id,
				u.name            AS created_by_name,

				c.id              AS crew_id,
				c.name            AS crew_name
			"
		);
	}

	/**
	 * Builds the FROM/JOIN clauses used by node-context queries.
	 *
	 * Joins:
	 * - root_causes_tree -> problems (required)
	 * - problems -> users (problem creator, required)
	 * - problems -> crews (required)
	 * - root_causes_tree -> users (node author, optional)
	 *
	 * @return string SQL FROM + JOIN clauses.
	 */
	private function baseNodeJoins(): string
	{
		return (
			"
			FROM root_causes_tree rct
			INNER JOIN problems p ON p.id = rct.problem_id
			INNER JOIN users u    ON u.id = p.created_by
			INNER JOIN crews c    ON c.id = p.crew_id
			LEFT JOIN users ua    ON ua.id = rct.author_id
			"
		);
	}

}
