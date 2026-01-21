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


    /**
     * Returns the currently authenticated (mock) user.
     *
     * This endpoint resolves the user identity from the `X-Mock-User-Id` HTTP header.
     * It is intended for development environments where no login/authentication
     * mechanism is implemented yet.
     *
     * The client MUST provide a valid `X-Mock-User-Id` header. The backend uses this
     * identifier to fetch the corresponding user and return it as the current user
     * context (equivalent to a `/me` endpoint in authenticated systems).
     *
     * Request:
     *  - Method: GET
     *  - Headers:
     *      - X-Mock-User-Id (int, required): Mock user identifier
     *
     * Responses:
     *  - 200 OK: User data returned successfully
     *  - 401 Unauthorized: Missing or invalid `X-Mock-User-Id` header
     *
     * Notes:
     *  - This endpoint SHOULD remain stable when real authentication is introduced.
     *  - When authentication is added, the user ID will be resolved from the
     *    authentication token instead of a mock header.
     *
     * @return array Prepared response payload containing the current user data.
     */
    public function me(): array
    {
        $userId = (int)($_SERVER['HTTP_X_MOCK_USER_ID'] ?? -1);

        if ($userId <= 0) {
            return (
                $this->jsonResponse(
                    ['message' => 'Missing X-Mock-User-Id'],
                    HTTP_UNAUTHORIZED
                )

            );
        }

        return ($this->getUser($userId));
    }
}
