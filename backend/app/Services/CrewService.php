<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CrewRepository;

final class CrewService
{
    public function __construct(
        private CrewRepository $repository
    ) {}

    /**
     * Retrieves all crews in a simplified representation.
     *
     * Fetches crew records from the repository and transforms them
     * into a minimal array structure containing only the identifier
     * and the name of each crew.
     *
     * @return array<int, array{
     *     id: int,
     *     name: string
     * }>
     */
    public function getAllCrews()
    {
        $rows = $this->repository->findAll();

        return (
            array_map(
                static fn($r) => [
                    'id' => $r['id'],
                    'name' => $r['name']
                ],
                $rows
            )
        );
    }
}
