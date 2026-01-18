<?php

declare(strict_types=1);

final class ProblemController extends AbstractController
{
    public function __construct(private ProblemService $service) {}

    /**
     * Returns a problem resource by its ID.
     *
     * Request:
     *  - Method: GET
     *  - Path: /problems/{id}
     *
     * Responses:
     *  - 200 OK                  on success
     *  - 404 Not Found           if the resource does not exist
     *
     * @param int $id The unique identifier of the problem resource.
     *
     * @return void
     */
    public function get(int $id): void
    {
        $problem = $this->service->getById($id);

        if ($problem === null)
        {
            $this->toJson(
                ['error' => 'Resource not found'],
                HTTP_NOT_FOUND
            );
            return;
        }

        $this->toJson(
            $problem,
            HTTP_OK
        );
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
        $this->storeAction(
            [$this, 'createProblem'],
            ['title', 'description'],
            ['title', 'description']
        );
    }

    /**
     * Creates a new problem using the service layer.
     *
     * @param array $data Associative array with keys 'title' and 'description'.
     *
     * @return int ID of the newly created problem.
     */
    private function createProblem(array $data): int
    {
        return ($this->service->create(
            $data['title'],
            $data['description']
        ));
    }
}
