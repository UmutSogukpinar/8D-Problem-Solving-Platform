<?php

declare(strict_types=1);

namespace App\Controllers;

use JsonException;

class BaseController
{
    /**
     * Handles a generic "get" action for retrieving a resource by its identifier.
     *
     * Executes the given retrieval callable and prepares a standardized
     * response payload along with the appropriate HTTP status code.
     * The actual JSON encoding and output are expected to be handled
     * by a higher-level response handler.
     *
     * Response behavior:
     *  - Returns the resource data with HTTP 200 if found
     *  - Returns an error payload with HTTP 404 if the resource is not found
     *
     * @param int      $id    The unique identifier of the resource.
     * @param callable $getFn Retrieval function.
     *                        Signature: function(int $id): mixed|null
     *                        Must return the resource data or null if not found.
     *
     * @return mixed Prepared response payload.
     */
    protected function get(int $id, Callable $getFn): mixed
    {
        $data = $getFn($id);

        if ($data === null)
        {
            return ($this->jsonResponse(
                ['error' => 'Resource not found'],
                HTTP_NOT_FOUND
            ));
        }

        return ($this->jsonResponse($data, HTTP_OK));
    }

    /**
     * Prepares a JSON response payload with the given HTTP status code.
     *
     * Sets the HTTP response status code and returns the given payload.
     * The actual JSON encoding and output are expected to be handled
     * by the caller or a higher-level response handler.
     *
     * @param mixed $payload Data to be returned as the response body.
     * @param int   $status  HTTP status code to set for the response.
     *
     * @return mixed The response payload.
     */
    protected function jsonResponse(mixed $payload, int $status = 200): mixed
    {
        http_response_code($status);
        return ($payload);
    }

    /**
     * Reads and decodes the JSON request body.
     *
     * @return array|null Decoded JSON as an associative array,
     *                     or null on failure.
     */
    public function readJsonBody(): ?array
    {
        $raw = file_get_contents('php://input');

        if ($raw === false || trim($raw) === '')
        {
            return (null);
        }

        try
        {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        }
        catch (JsonException) 
        {
            return (null);
        }

        if (!is_array($data)) 
        {
            return (null);
        }

        return ($data);
    }
}
