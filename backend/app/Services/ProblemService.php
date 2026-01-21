<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProblemRepository;

final class ProblemService
{
    public function __construct(
        private ProblemRepository $repo
    ) {}

    /**
     * Creates a new record inside a database transaction.
     *
     * @param string $title     The title of the entity to be created.
     * @param string $desc      The description of the entity to be created.
     * @param int    $crewId    The id that belongs to the crew 
     *                           which interact with the problem
     * @param int   $userId     The id that belongs to the person
     *                           who insert problem
     *
     * @return int The ID of the newly created record.
     *
     * @throws Throwable If an error occurs during the creation process.
     *                   The transaction is rolled back before rethrowing.
     */
    public function create(string $title, string $desc, int $crewId, int $userId): int
    {
        return ($this->repo->create($title, $desc, $crewId, $userId));
    }

    /**
     * Retrieves a problem by its identifier.
     *
     * @param int $id
     *
     * @return array|null The problem data, or null if not found.
     */
    public function getById(int $id): ?array
    {
        return ($this->repo->findById($id));
    }

    /**
     * Retrieves all problems from the repository.
     *
     * @return array List of all problem entities.
     *                  If there is no records
     *                  returns empty list
     */
    public function getAllProblems(): array
    {
        $rows = $this->repo->findAll();

        return (array_map(static fn($r) => [
            'id' => $r['id'],
            'title' => $r['title'],
            'description' => $r['description'],
            'createdAt' => $r['created_at'],
            'createdBy' => [
                'id' => $r['created_by_id'],
                'name' => $r['created_by_name'],
            ],
            'crew' => [
                'id' => $r['crew_id'],
                'name' => $r['crew_name'],
            ],
        ], $rows));
    }
}
