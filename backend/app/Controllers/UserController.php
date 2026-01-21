<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;

final class UserController extends BaseController
{
    public function __construct(
        private UserService $service
    ) {}

    /**
     * Health check endpoint for the UserController.
     *
     * Request:
     *  - Method: GET
     *
     * Responses:
     *  - 200 OK on success
     *
     * @return mixed Prepared response payload.
     */
    public function health(): mixed
    {
        return (
            $this->jsonResponse(
                ['status' => 'UserController is healthy'],
                HTTP_OK
            )
        );
    }

    /**
     * Retrieves a single user by its identifier.
     *
     * Request:
     *  - Method: GET
     *  - Params:
     *      - id (int): User identifier
     *
     * Responses:
     *  - 200 OK on success
     *
     * @return mixed Prepared response payload containing user data.
     */
    public function getUser(int $id): mixed
    {
        return (
            $this->jsonResponse(
                $this->service->getUser($id),
                HTTP_OK
            )
        );
    }
}
