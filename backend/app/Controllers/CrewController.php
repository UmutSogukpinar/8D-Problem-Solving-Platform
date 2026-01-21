<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\CrewService;


class CrewController extends BaseController
{
    public function __construct(
        private Request $request,
        private CrewService $service
    )  {}

    /**
     * Health check endpoint for the CrewController.
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
     * Retrieves all crews.
     *
     * Request:
     *  - Method: GET
     *
     * Responses:
     *  - 200 OK on success
     *
     * @return mixed Prepared response payload containing the list of crews.
     */
    public function getAllCrews(): mixed
    {
        return ($this->jsonResponse(
            $this->service->getAllCrews(),
            HTTP_OK
        ));
    }

}