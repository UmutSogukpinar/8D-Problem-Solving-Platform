<?php

declare(strict_types=1);

final class ProblemService
{
    public function __construct(
        private ProblemRepository $repo,
        private PDO $pdo
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
        // Transaction begins
        $this->pdo->beginTransaction();

        try
        {
            $id = $this->repo->create($title, $desc);
            $this->pdo->commit();
            return ($id);
        }
        catch (Throwable $e)
        {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
