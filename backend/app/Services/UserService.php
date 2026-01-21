<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

final class UserService
{
    public function __construct(
        private UserRepository $repo
    ) {}

    /**
     * Retrieves a single user with its associated crew information.
     *
     * Fetches the user record by identifier from the repository and
     * returns a structured array including basic user data and
     * the related crew details.
     *
     * @param int $id User identifier
     *
     * @return array Prepared user data payload
     */
    public function getUser(int $id): array
    {
        $r = $this->repo->findById($id);

        return (
            [
                'user_id' => $r['user_id'],
                'user_name' => $r['user_name'],
                'user_email' => $r['user_email'],
                'crew' => [
                    'crew_id' => $r['crew_id'],
                    'crew_name' => $r['crew_name'],
                ],
            ]
        );
    }
}
