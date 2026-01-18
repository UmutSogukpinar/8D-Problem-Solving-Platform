<?php

declare(strict_types=1);

final class AbstractController
{

    /**
     * Handles a generic "store" action for creating a resource.
     *
     * Reads and validates the JSON request body, executes the provided creation
     * callable, and sends a standardized JSON response.
     *
     * Validation rules:
     *  - Only fields listed in $allowedKeys are permitted
     *  - All fields listed in $requiredKeys must exist
     *  - Required fields must contain non-empty string values
     *
     * Responses:
     *  - 201 Created                  on success
     *  - 400 Bad Request              if the request body is not valid JSON
     *  - 422 Unprocessable Entity     if validation fails
     *  - 500 Internal Server Error    on unexpected errors
     *
     * @param callable      $createFn     Function responsible for creating the resource.
     *                                   Signature: function(array $data): int
     *                                   Must return the created resource identifier.
     * @param array         $requiredKeys List of required field names.
     * @param array         $allowedKeys  List of allowed field names.
     * @param callable|null $responseFn   Optional function to build the response payload.
     *                                   Signature: function(int $id, array $data): array
     *
     * @return void
     */
    protected function storeAction(
        callable $createFn,
        array $requiredKeys,
        array $allowedKeys,
        ?callable $responseFn = null
    ): void
    {
        $data = $this->readJsonBody();

        if ($data === null)
        {
            $this->toJson(
                ['error' => 'Invalid JSON body'],
                HTTP_BAD_REQUEST
            );
            return ;
        }

        $errors = $this->runValidators(
        [
            fn (array $d) => $this->validateRequired($d, $requiredKeys),
            fn (array $d) => $this->validateAllowed($d, $allowedKeys),
        ],
        $data
    );

        if (!empty($errors))
        {
            $this->toJson(
                ['error' => 'Validation failed', 'fields' => $errors],
                HTTP_UNPROCESSABLE_ENTITY
            );
            return ;
        }

        try
        {
            $id = $createFn($data);

            $payload = ($responseFn !== null)
                ? $responseFn($id, $data)
                : ['id' => $id];

            $this->toJson(
                $payload,
                HTTP_CREATED
            );
        }
        catch (Throwable)
        {
            $this->toJson(
                ['error' => 'Internal server error'],
                HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Runs multiple validation functions and merges their error results.
     *
     * If any validator returns errors, the process stops early.
     * 
     * @param array $validators List of validator callables.
     * @param array $data       Input data to validate.
     *
     * @return array<string, string> Combined validation errors.
     */
    private function runValidators(array $validators, array $data): array
    {
        $errors = [];

        foreach ($validators as $validator)
        {
            $errors = array_merge(
                $errors,
                $validator($data)
            );

            if (!empty($errors))
            {
                break;
            }
        }

        return ($errors);
    }

    /**
     * Validates that required fields exist and contain non-empty string values.
     *
     * @param array $data         The input data to validate.
     * @param array $requiredKeys List of required field names.
     *
     * @return array An associative array of validation errors indexed by field name.
     *               An empty array indicates that all required fields are valid.
     */
    private function validateRequired(array $data, array $requiredKeys): array
    {
        $errors = [];

        foreach ($requiredKeys as $key)
        {
            if (!array_key_exists($key, $data))
            {
                $errors[$key] = 'Missing field';
                continue;
            }

            if (!is_string($data[$key]) || trim($data[$key]) === '')
            {
                $errors[$key] = 'Must be a non-empty string';
            }
        }

        return ($errors);
    }

    /**
     * Validates that no unexpected fields are present in the payload.
     *
     * @param array $data        The input data to validate.
     * @param array $allowedKeys List of allowed field names.
     *
     * @return array<string, string> Validation errors indexed by field name.
     */
    private function validateAllowed(array $data, array $allowedKeys): array
    {
        $errors = [];
        $extraKeys = array_diff(array_keys($data), $allowedKeys);

        foreach ($extraKeys as $key)
        {
            $errors[$key] = 'Unexpected field';
        }

        return ($errors);
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
    protected function toJson(array $payload, int $status): void
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
