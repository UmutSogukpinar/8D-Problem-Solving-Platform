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
                'userId' => $r['user_id'],
                'username' => $r['user_name'],
                'userEmail' => $r['user_email'],
                'crew' => [
                    'id' => $r['crew_id'],
                    'name' => $r['crew_name'],
                ],
            ]
        );
    }
}
