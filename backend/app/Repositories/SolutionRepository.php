<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class SolutionRepository
{
	public function __construct(private PDO $pdo) {}

	/**
	 * Persists a new solution record in the database.
	 *
	 * @param int      $problemId   Related problem identifier.
	 * @param int      $rootCauseId Related root cause node identifier.
	 * @param string   $desc        Solution description.
	 * @param int      $authorId    Author identifier.
	 *
	 * @return int ID of the newly created solution.
	 */
	public function create(
		int $problemId,
		int $rootCauseId,
		string $desc,
		int $authorId
	): int
	{
		$stmt = $this->pdo->prepare(
			"
			INSERT INTO solutions (problem_id, root_cause_id, author_id, description, created_at)
			VALUES (:p, :rc, :a, :d, NOW())
			"
		);

		$stmt->execute([
			'p' => $problemId,
			'rc' => $rootCauseId,
			'a' => $authorId,
			'd' => $desc,
		]);

		return ((int)$this->pdo->lastInsertId());
	}

	/**
	 * Retrieves a solution by its unique identifier.
	 *
	 * @param int $id Solution identifier.
	 *
	 * @return array|null Solution data as an associative array, or null if not found.
	 */
	public function findById(int $id): ?array
	{
		$stmt = $this->pdo->prepare(
			"
			SELECT
				s.id,
				s.problem_id,
				s.root_cause_id,
				s.description,
				s.created_at,

				u.id   AS author_id,
				u.name AS author_name

			FROM solutions s
			LEFT JOIN users u ON u.id = s.author_id
			WHERE s.id = :id
			LIMIT 1
			"
		);

		$stmt->execute(['id' => $id]);

		return ($stmt->fetch(PDO::FETCH_ASSOC) ?: null);
	}

	/**
	 * Retrieves all solutions for a given problem.
	 *
	 * @param int $problemId Related problem identifier.
	 *
	 * @return array<int, array<string, mixed>> List of solutions as associative arrays.
	 */
	public function findAllByProblemId(int $problemId): array
	{
		$stmt = $this->pdo->prepare(
			"
			SELECT
				s.id,
				s.problem_id,
				s.root_cause_id,
				s.description,
				s.created_at,

				rc.description AS root_cause_description,
				rc.is_root_cause,

				u.id   AS author_id,
				u.name AS author_name

			FROM solutions s
			INNER JOIN root_causes_tree rc ON rc.id = s.root_cause_id
			LEFT JOIN users u ON u.id = s.author_id
			WHERE s.problem_id = :pid
			ORDER BY s.created_at DESC
			"
		);

		$stmt->execute(['pid' => $problemId]);

		return ($stmt->fetchAll(PDO::FETCH_ASSOC));
	}


}
