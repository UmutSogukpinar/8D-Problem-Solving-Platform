<?php

declare(strict_types=1);

namespace App\Controllers;

use Throwable;
use App\Services\RootCausesTreeService;

class RootCausesTreeController extends BaseController
{
    public function __construct(private RootCausesTreeService $service) {}

    /**
     * Returns a root cause tree node by its ID.
     *
     * Request:
     *  - Method: GET
     *
     * Responses:
     *  - 200 OK                  on success
     *  - 404 Not Found           if the resource does not exist
     *
     * @param int $id The unique identifier of the root cause node.
     *
     * @return void
     */
    public function getRootCauseNode(int $id): void
    {
        $this->get($id, [$this->service, 'getById']);
    }

    /**
     * Returns the root cause tree for a specific problem.
     *
     * Request:
     *  - Method: GET
     *
     * Responses:
     *  - 200 OK                  on success
     *
     * @param int $problemId The unique identifier of the problem.
     *
     * @return void
     */
    public function getTreeByProblemId(int $problemId): void
    {
        $this->toJson(
            $this->service->getTreeByProblemId($problemId),
            HTTP_OK
        );
    }

    /**
     * Handles the creation of a new root cause tree node.
     *
     * Request:
     *  - Method: POST
     *  - Body (JSON):
     *      {
     *          "problem_id": int,
     *          "description": string,
     *          "parent_id": int | null
     *      }
     *
     * Responses:
     *  - 201 Created            on success
     *  - 400 Bad Request        if the request body is not valid JSON
     *  - 422 Unprocessable      if required fields are missing/invalid
     *  - 500 Internal Server    on unexpected errors
     *
     * @return void
     */
    public function store(): void
    {

        // ======== Read Json body ========
        $data = $this->readJsonBody();

        if ($data === null) 
        {
            $this->toJson(['error' => 'Invalid or missing JSON body'], HTTP_BAD_REQUEST);
            return;
        }

        // ======== Validate data from JSON ========
        $errors = [];

        if (!isset($data['problem_id']) || !is_int($data['problem_id'])) 
        {
            $errors['problem_id'] = 'Required field and must be an integer';
        }
        if (empty($data['description']) || !is_string($data['description']))
        {
            $errors['description'] = 'Required field and must be a non-empty string';
        }
        if (isset($data['parent_id']) && !is_int($data['parent_id']))
        {
            $errors['parent_id'] = 'Must be an integer or null';
        }

        if (!empty($errors))
        {
            $this->toJson(['errors' => $errors], HTTP_UNPROCESSABLE_ENTITY);
            return;
        }

        // ======== Create the root cause node ========
        try
        {
            $parentId = $data['parent_id'] ?? null;

            $newId = $this->service->create(
                $data['problem_id'],
                $parentId,
                $data['description']
            );

            $this->toJson(
                [
                    'id' => $newId,
                    'message' => 'Root cause node created'
                ], 
                HTTP_CREATED
            );

        } 
        catch (Throwable)
        {
            $this->toJson(['error' => 'Internal Server Error'], 500);
        }
    }

}
