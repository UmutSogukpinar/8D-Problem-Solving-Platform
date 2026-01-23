<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SolutionRepository;

final class SolutionService
{
	public function __construct(
		private SolutionRepository $repo
	) {}

	/**
	 * Creates a new solution record.
	 *
	 * @param int      $problemId   Related problem identifier.
	 * @param int      $rootCauseId Related root cause tree node identifier.
	 * @param string   $desc        Solution description.
	 * @param int|null $authorId    Optional author identifier.
	 *
	 * @return int The ID of the newly created solution.
	 */
	public function create(
		int $problemId,
		int $rootCauseId,
		string $desc,
		?int $authorId
	): int
	{
		return ($this->repo->create($problemId, $rootCauseId, $desc, $authorId));
	}

	/**
	 * Retrieves a solution by its identifier.
	 *
	 * @param int $id Solution identifier.
	 *
	 * @return array{
	 *     id: int,
	 *     problemId: int,
	 *     rootCauseId: int,
	 *     rootCauseDescription: string|null,
	 *     isRootCause: bool|null,
	 *     description: string,
	 *     createdAt: string,
	 *     author: array{id: int, name: string}|null
	 * }|null
	 */
	public function getById(int $id): ?array
	{
		$row = $this->repo->findById($id);

		if (!$row)
		{
			return (null);
		}

		return ($this->mapRow($row));
	}

	/**
	 * Retrieves all solutions for a given problem.
	 *
	 * @param int $problemId Related problem identifier.
	 *
	 * @return array<int, array{
	 *     id: int,
	 *     problemId: int,
	 *     rootCauseId: int,
	 *     rootCauseDescription: string|null,
	 *     isRootCause: bool|null,
	 *     description: string,
	 *     createdAt: string,
	 *     author: array{id: int, name: string}|null
	 * }>
	 */
	public function getAllByProblemId(int $problemId): array
	{
		$rows = $this->repo->findAllByProblemId($problemId);

		return (array_map(fn($r) => $this->mapRow($r), $rows));
	}

	/**
	 * Maps a raw repository row into API-ready structure.
	 *
	 * Expected extra fields (optional, depending on SELECT):
	 *  - root_cause_description
	 *  - is_root_cause
	 *
	 * @param array $r Raw row.
	 *
	 * @return array{
	 *     id: int,
	 *     problemId: int,
	 *     rootCauseId: int,
	 *     rootCauseDescription: string|null,
	 *     isRootCause: bool|null,
	 *     description: string,
	 *     createdAt: string,
	 *     author: array{id: int, name: string}|null
	 * }
	 */
	private function mapRow(array $r): array
	{
		$author = null;

		if (!empty($r['author_id']))
		{
			$author = [
				'id' => (int)$r['author_id'],
				'name' => (string)$r['author_name'],
			];
		}

		$rootCauseDescription = null;

		if (array_key_exists('root_cause_description', $r) && $r['root_cause_description'] !== null)
		{
			$rootCauseDescription = (string)$r['root_cause_description'];
		}

		$isRootCause = null;

		if (array_key_exists('is_root_cause', $r) && $r['is_root_cause'] !== null)
		{
			$isRootCause = ((int)$r['is_root_cause'] === 1);
		}

		return (
			[
				'id' => (int)$r['id'],
				'problemId' => (int)$r['problem_id'],
				'rootCauseId' => (int)$r['root_cause_id'],

				'rootCauseDescription' => $rootCauseDescription,
				'isRootCause' => $isRootCause,

				'description' => (string)$r['description'],
				'createdAt' => (string)$r['created_at'],
				'author' => $author,
			]
		);
	}
}
