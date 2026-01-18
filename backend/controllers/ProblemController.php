<?php

declare(strict_types=1);

final class ProblemController
{
    public function __construct(private ProblemService $service) {}

    /**
     * Creates a new problem resource.
     *
     * Request:
     *  - Method: POST
     *  - Body (JSON): { "title": string, "description": string }
     *
     * Responses:
     *  - 201 Created            on success
     *  - 400 Bad Request        if the request body is not valid JSON
     *  - 422 Unprocessable      if required fields are missing/empty 
     *                           or unexpected fields are present
     *  - 500 Internal Server    on unexpected errors
     *
     * @return void
     */
    public function store(): void
    {
        $data = $this->readJsonBody();

        if ($data === null)
        {
            $this->toJson(['error' => 'Invalid JSON body'], HTTP_BAD_REQUEST);
            return ;
        }

        $validated = $this->validateCreatePayload($data);

        if ($validated['ok'] === false) 
        {
            $payload = ['error' => $validated['error']];

            if (isset($validated['fields'])) 
            {
                $payload['fields'] = $validated['fields'];
            }

            $this->toJson($payload, $validated['status']);
            return ;
        }

        try
        {
            $id = $this->service->create(
                $validated['title'],
                $validated['description']
            );

            $this->toJson(
                [
                'id' => $id,
                'title' => $validated['title'],
                'description' => $validated['description']
                ], 
                HTTP_CREATED);
        } 
        catch (Throwable $e)
        {
            if (function_exists('logMessage'))
            {
                logMessage(ERROR, 'POST /problems failed: ' . $e->getMessage());
            }

            $this->toJson(['error' => 'Internal server error'], 500);
            return ;
        }
    }

    /**
     * Reads and decodes the JSON request body.
     *
     * @return array|null Decoded JSON as an associative array,
     *                     or null on failure.
     */
    private function readJsonBody(): ?array
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

    /**
     * Validates payload for creating a problem.
     *
     * Enforces a strict schema: only "title" and "description" keys are allowed.
     *
     * @param array $data Raw request payload.
     *
     * @return array Validation result:
     *               - [
     *                  'ok' => true, 
     *                  'title' => string,
     *                  'description' => string
     *                 ]
     *               - [
     *                  'ok' => false,
     *                  'status' => int,
     *                  'error' => string, 
     *                  'fields' => array<string> (optional)
     *                 ]
     */
    private function validateCreatePayload(array $data): array
    {
        $allowedKeys = ['title', 'description'];
        $extraKeys = array_diff(array_keys($data), $allowedKeys);

        if (!empty($extraKeys)) 
        {
            return ([
                'ok' => false,
                'status' => HTTP_UNPROCESSABLE_ENTITY,
                'error' => 'Unexpected fields',
                'fields' => array_values($extraKeys)
            ]);
        }

        $title = trim((string) ($data['title'] ?? ''));
        $desc  = trim((string) ($data['description'] ?? ''));

        if ($title === '' || $desc === '') 
        {
            return ([
                'ok' => false,
                'status' => HTTP_UNPROCESSABLE_ENTITY,
                'error' => 'title and description are required'
            ]);
        }

        return ([
            'ok' => true,
            'title' => $title,
            'description' => $desc
        ]);
    }

    /**
     * Sends the given payload as a JSON HTTP response.
     *
     * Sets the HTTP status code and JSON content type header, then encodes and outputs
     * the payload as JSON. If JSON encoding fails, a 500 response is sent.
     *
     * @param array $payload Data to be sent as JSON.
     * @param int   $status  HTTP status code.
     *
     * @return void
     */
    private function toJson(array $payload, int $status): void
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
}
