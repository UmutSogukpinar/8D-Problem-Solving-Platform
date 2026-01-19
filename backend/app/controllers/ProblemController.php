<?php

declare(strict_types=1);

namespace App\Controllers;

use Throwable;
use App\Services\ProblemService;

final class ProblemController extends BaseController
{
    public function __construct(private ProblemService $service) {}

    /**
     * Returns a problem resource by its ID.
     *
     * Request:
     *  - Method: GET
     *
     * Responses:
     *  - 200 OK                  on success
     *  - 404 Not Found           if the resource does not exist
     *
     * @param int $id The unique identifier of the problem resource.
     *
     * @return mixed Prepared response payload.
     */
    public function getProblem(int $id): mixed
    {
        return ($this->get($id, [$this->service, 'getById']));
    }

    /**
     * Handles the creation of a new problem resource.
     *
     * Request:
     *  - Method: POST
     * 
     * Expected JSON body:
     *  - title: string (required, non-empty)
     *  - description: string (required, non-empty)
     *
     * Responses:
     *  - 201 Created            on success
     *  - 400 Bad Request        if the request body is not valid JSON
     *  - 422 Unprocessable      if required fields are missing/empty
     *                              or unexpected fields are present
     *  - 500 Internal Server    on unexpected errors
     *
     * @return mixed Prepared response payload.
     */
    public function store(): mixed
    {
        // ======== Read JSON body ========
        $data = $this->readJsonBody();

        if ($data === null)
        {
            return ($this->jsonResponse(
                ['error' => 'Invalid or missing JSON body'],
                HTTP_BAD_REQUEST
            ));
        }

        // ======== Validate Data from JSON ========
        $errors = [];

        if (empty($data['title']) || !is_string($data['title']))
        {
            $errors['title'] = 'Title is required and must be a string';
        }
        if (empty($data['description']) || !is_string($data['description'])) 
        {
            $errors['description'] = 'Description is required and must be a string';
        }

        $allowed = ['title', 'description'];
        $extras = array_diff(array_keys($data), $allowed);
        if (!empty($extras))
        {
            $errors['general'] = 'Unexpected fields: ' . implode(', ', $extras);
        }

        if (!empty($errors)) 
        {
            return ($this->jsonResponse(
                ['errors' => $errors],
                HTTP_UNPROCESSABLE_ENTITY
            ));
        }

        // ======== Create Problem ========
        try
        {
            $newId = $this->service->create(
                $data['title'], 
                $data['description']
            );

            return ($this->jsonResponse(
                [
                    'id' => $newId,
                    'message' => 'Problem created successfully'
                ],
                HTTP_CREATED
            ));
        } 
        catch (Throwable) 
        {
            return ($this->jsonResponse(
                ['error' => 'Internal Server Error'], 
                HTTP_INTERNAL_SERVER_ERROR
            ));
        }
    }
}
