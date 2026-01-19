<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\Request;
use App\Services\RootCausesTreeService;
use App\Exceptions\BadRequestException;

class RootCausesTreeController extends BaseController
{
    public function __construct(
        private Request $request,
        private RootCausesTreeService $service
    ) {}

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
        $data = $this->request->all();

        // ======== Validate data from JSON ========
        Validator::validate($data, [
            'problem_id'  => 'required|int',
            'description' => 'required|string',
            'parent_id'   => 'nullable|int'
        ]);

        // ======== Create the root cause node ========
        $newId = $this->service->create(
            (int) $data['problem_id'],
            $data['parent_id'],
            $data['description']
        );

        // ======== Return Response ========
        return ($this->jsonResponse(
            [
                'id' => $newId,
                'message' => 'Root cause node created'
            ],
            HTTP_CREATED
        ));
    }

}
