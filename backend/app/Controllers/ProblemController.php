<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\ProblemService;
use App\Core\Validator;

final class ProblemController extends BaseController
{
    public function __construct(
        private Request $request,
        private ProblemService $service
    ) {}

    /**
     * Health check endpoint for the ProblemController.
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
        return ($this->jsonResponse(
            ['status' => 'ProblemController is healthy'],
            HTTP_OK
        ));
    }

    /**
     * Returns all problem resources.
     *
     * Request:
     *  - Method: GET
     *
     * Responses:
     *  - 200 OK                  on success
     *
     * @return mixed Prepared response payload.
     */
    public function getAllProblems(): mixed
    {
        return ($this->jsonResponse(
            $this->service->getAllProblems(),
            HTTP_OK
        ));
    }

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
        $data = $this->request->all();

        // ======== Validate Data from JSON ========
        Validator::validate($data, [
            'title'       => 'required|string',
            'description' => 'required|string',
            'crew_id'     => 'required|int',
            'user_id'     => 'required|int'
        ]);

        // ======== Create Problem ========
        $newId = $this->service->create(
            $data['title'], 
            $data['description'],
            $data['crew_id'],
            $data['user_id']
        );

        // ======== Return Response ========
        return ($this->jsonResponse(
            [
                'id' => $newId,
                'message' => 'Problem created successfully'
            ],
            HTTP_CREATED
        ));
    }

}
