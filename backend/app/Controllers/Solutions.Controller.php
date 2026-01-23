<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Validator;
use App\Services\SolutionService;

final class SolutionController extends BaseController
{
	public function __construct(
		private Request $request,
		private SolutionService $service
	) {}

	/**
	 * Health check endpoint for the SolutionController.
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
			['status' => 'SolutionController is healthy'],
			HTTP_OK
		));
	}

	/**
	 * Returns a solution resource by its ID.
	 *
	 * Request:
	 *  - Method: GET
	 *
	 * Responses:
	 *  - 200 OK        on success
	 *  - 404 Not Found if the resource does not exist
	 *
	 * @param int $id The unique identifier of the solution resource.
	 *
	 * @return mixed Prepared response payload.
	 */
	public function getSolution(int $id): mixed
	{
		return ($this->get($id, [$this->service, 'getById']));
	}

	/**
	 * Handles the creation of a new solution resource.
	 *
	 * Request:
	 *  - Method: POST
	 *
	 * Expected JSON body:
	 *  - problem_id: int (required)
	 *  - root_cause_id: int (required)
	 *  - description: string (required, non-empty)
	 *  - author_id: int
	 *
	 * Responses:
	 *  - 201 Created          on success
	 *  - 400 Bad Request      if the request body is not valid JSON
	 *  - 422 Unprocessable    if required fields are missing/empty
	 *                           or unexpected fields are present
	 *  - 500 Internal Server  on unexpected errors
	 *
	 * @return mixed Prepared response payload.
	 */
	public function store(): mixed
	{
		$data = $this->request->all();

		Validator::validate($data, [
			'problem_id'    => 'required|int',
			'root_cause_id' => 'required|int',
			'description'   => 'required|string',
			'author_id'     => 'required|int',
		]);

		$newId = $this->service->create(
			(int)$data['problem_id'],
			(int)$data['root_cause_id'],
			(string)$data['description'],
			(int)$data['author_id'],
		);

		return ($this->jsonResponse(
			[
				'id' => $newId,
				'message' => 'Solution created successfully',
			],
			HTTP_CREATED
		));
	}

}
