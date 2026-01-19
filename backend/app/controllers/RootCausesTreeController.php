<?php

declare(strict_types=1);

namespace App\Controllers;

use Throwable;
use App\Services\RootCausesTreeService;

class RootCausesTreeController extends BaseController
{
    public function __construct(private RootCausesTreeService $service) {}

    /**
     * Retrieves a root cause tree node by its identifier.
     *
     * Request:
     * - Method: GET
     * 
     * Responses:
     * - 200 OK                  on success
     * - 404 Not Found            if the node does not exist
     *
     * @param int $id The unique identifier of the root cause node.
     *
     * @return mixed Prepared response payload.
     */
    public function getRootCauseNode(int $id): mixed
    {
        return ($this->get($id, [$this->service, 'getById']));
    }

    // TODO: Check later
    /**
     * Retrieves the root cause tree for a given problem.
     *
     * Request:
     * - Method: GET
     * 
     * Responses:
     * - 200 OK                  on success
     * - 404 Not Found            if the problem does not exist
     *
     * @param int $problemId The unique identifier of the problem.
     *
     * @return mixed Prepared response payload.
     */
    public function getTreeByProblemId(int $problemId): mixed
    {
        return ($this->jsonResponse(
            $this->service->getTreeByProblemId($problemId),
            HTTP_OK
        ));
    }

    /**
     * Creates a new root cause tree node.
     *
     * Request:
     * - Method: POST
     * 
     * Expected JSON body:
     *  - problem_id: int (required)
     *  - description: string (required, non-empty)
     *  - parent_id: int|null (optional)
     *
     * Responses:
     *  - 201 Created            on success
     *  - 400 Bad Request        if the request body is not valid JSON
     *  - 422 Unprocessable      if required fields are missing/invalid
     *  - 500 Internal Server    on unexpected errors
     *
     * @return mixed Prepared response payload.
     */
    public function store(): mixed
    {

        // ======== Read Json body ========
        $data = $this->readJsonBody();

        if ($data === null) 
        {
            return ($this->jsonResponse(
                ['error' => 'Invalid or missing JSON body'],
                HTTP_BAD_REQUEST))
            ;
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
            return ($this->jsonResponse(
                ['errors' => $errors], 
                HTTP_UNPROCESSABLE_ENTITY)
            );
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

            return ($this->jsonResponse(
                [
                    'id' => $newId,
                    'message' => 'Root cause node created'
                ], 
                HTTP_CREATED
            ));

        } 
        catch (Throwable)
        {
            return ($this->jsonResponse(
                ['error' => 'Internal Server Error'],
                HTTP_INTERNAL_SERVER_ERROR)
            );
        }
    }

}
