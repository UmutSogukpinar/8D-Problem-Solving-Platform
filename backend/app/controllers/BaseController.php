<?php

declare(strict_types=1);

namespace App\Controllers;

use JsonException;

class BaseController
{
    /**
     * Handles a generic "get" action for retrieving a resource by ID.
     *
     * Executes the provided retrieval callable and sends a standardized JSON response.
     *
     * Responses:
     *  - 200 OK          on success
     *  - 404 Not Found   if the resource does not exist
     *
     * @param int      $id      The unique identifier of the resource.
     * @param callable $getFn   Function responsible for retrieving the resource.
     *                          Signature: function(int $id): ?array
     *                          Must return the resource data as an associative array,
     *                          or null if not found.
     *
     * @return void
     */
    protected function get(int $id, Callable $getFn): void
    {
        $data = $getFn($id);

        if ($data === null)
        {
            $this->toJson(
                ['error' => 'Resource not found'],
                HTTP_NOT_FOUND
            );
            return ;
        }

        $this->toJson(
            $data,
            HTTP_OK
        );
    }

    /**
     * Sends the given payload as a JSON HTTP response.
     *
     * Sets the HTTP status code and JSON content type header, then encodes and outputs
     * the payload as JSON. If JSON encoding fails, a 500 response is sent.
     *
     * @param mixed $payload Data to be encoded and sent as JSON.
     * @param int   $status  HTTP status code.
     *
     * @return void
     */
    protected function toJson(mixed $payload, int $status): void
    {
        if (!headers_sent()) 
        {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }

        try 
        {
            echo json_encode($payload, JSON_THROW_ON_ERROR);
        }
        catch (JsonException) 
        {
            if (!headers_sent()) 
            {
                http_response_code(HTTP_INTERNAL_SERVER_ERROR);
            }

            echo '{"error":"JSON encoding failed"}';
        }
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
