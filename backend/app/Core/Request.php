<?php

declare(strict_types=1);

namespace App\Core;

use JsonException;

use App\Exceptions\BadRequestException;

final class Request
{
    /**
     * Parsed request body data.
     *
     * @var array<string, mixed>
     */
    private array $body;

    /**
     * Query parameters (typically from $_GET).
     *
     * @var array<string, mixed>
     */
    private array $query;

    /**
     * Creates a Request instance and resolves input data once.
     *
     * @return void
     *
     * @throws BadRequestException If the JSON body is malformed
     */
    public function __construct()
    {
        $this->query = $_GET;
        $this->body = $this->resolveBody();
    }

    /**
     * Resolves the request body into an associative array.
     *
     * Returns form data if present; otherwise attempts to decode a JSON body.
     * If the body is empty, an empty array is returned.
     *
     * @return array<string, mixed>
     *
     * @throws BadRequestException If JSON is malformed or not an object/array
     */
    private function resolveBody(): array
    {
        if (!empty($_POST))
        {
            return ($_POST);
        }

        $raw = file_get_contents('php://input');

        if ($raw === false || trim($raw) === '')
        {
            return ([]);
        }

        try
        {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        }
        catch (JsonException $e)
        {
            throw new BadRequestException(
                'Invalid JSON format: ' . $e->getMessage()
            );
        }

        if (!is_array($data))
        {
            throw new BadRequestException(
                'Invalid JSON body: Expected an object/array.'
            );
        }

        return ($data);
    }

    /**
     * Returns a value from the parsed request body.
     *
     * @param string $key     Body key to retrieve
     * @param mixed  $default Default value if the key is missing
     *
     * @return mixed The value for the given key or the default
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return ($this->body[$key] ?? $default);
    }

    /**
     * Returns a value from the query parameters.
     *
     * @param string $key     Query key to retrieve
     * @param mixed  $default Default value if the key is missing
     *
     * @return mixed The value for the given key or the default
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return ($this->query[$key] ?? $default);
    }

    /**
     * Returns the full parsed request body.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return ($this->body);
    }
}
