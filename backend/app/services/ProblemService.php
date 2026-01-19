<?php

declare(strict_types=1);

final class ProblemService
{
    public function __construct(
        private ProblemRepository $repo
    ) {}

    /**
     * Creates a new record inside a database transaction.
     *
     * @param string $title The title of the entity to be created.
     * @param string $desc  The description of the entity to be created.
     *
     * @return int The ID of the newly created record.
     *
     * @throws Throwable If an error occurs during the creation process.
     *                   The transaction is rolled back before rethrowing.
     */
    public function create(string $title, string $desc): int
    {
        return ($this->repo->create($title, $desc));
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
}
