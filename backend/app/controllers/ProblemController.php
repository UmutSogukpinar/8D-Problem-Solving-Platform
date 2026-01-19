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
     * @return void
     */
    public function getProblem(int $id): void
    {
        $this->get($id, [$this->service, 'getById']);
    }

    /**
     * Handles the creation of a new problem resource.
     *
     * Request:
     *  - Method: POST
     *  - Body (JSON): { "title": string, "description": string }
     *
     * Responses:
     *  - 201 Created            on success
     *  - 400 Bad Request        if the request body is not valid JSON
     *  - 422 Unprocessable      if required fields are missing/empty
     *                              or unexpected fields are present
     *  - 500 Internal Server    on unexpected errors
     *
     * @return void
     */
    public function store(): void
    {
        // ======== Read JSON body ========
        $data = $this->readJsonBody();

        if ($data === null)
        {
            $this->toJson(['error' => 'Invalid or missing JSON body'], HTTP_BAD_REQUEST);
            return;
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
            $this->toJson(['errors' => $errors], HTTP_UNPROCESSABLE_ENTITY);
            return ;
        }

        // ======== Create Problem ========
        try
        {
            $newId = $this->service->create(
                $data['title'], 
                $data['description']
            );

            $this->toJson(
                [
                    'id' => $newId,
                    'message' => 'Problem created successfully'
                ],
                HTTP_CREATED
            );
        } 
        catch (Throwable) 
        {
            $this->toJson(
                ['error' => 'Internal Server Error'], 
                HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
