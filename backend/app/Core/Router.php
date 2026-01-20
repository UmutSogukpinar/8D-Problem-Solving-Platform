<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use JsonException;

final class Router
{
    public function __construct(private Container $container) {}

    /**
     * Registered routes indexed by HTTP method and normalized path.
     *
     * @var array<string, array<string, callable|array>>
     */
    private array $routes = [];

    /**
     * Registers a GET route.
     *
     * Example:
     *  $router->get('/problems', [ProblemController::class, 'index']);
     *
     * @param string         $path    Request path (e.g. "/health")
     * @param callable|array $handler Route handler
     *
     * @return void
     */
    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    /**
     * Dispatches the current HTTP request.
     *
     * Resolves the HTTP method and request path from the server environment,
     * finds a matching route handler, invokes it, and outputs the returned
     * payload as JSON.
     *
     * If no route matches, a JSON 404 response is returned.
     * 
     * @throws RuntimeException If the route handler cannot be invoked
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = $this->normalize(
            parse_url(
                $_SERVER['REQUEST_URI'] ?? '/', 
                PHP_URL_PATH) ?: '/'
        );

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null)
        {
            $this->sendJson(['error' => 'Route not found'], 404);
            return;
        }

        $this->invoke($handler);
    }

    /**
     * Invokes the given route handler and outputs its result as JSON.
     *
     * - If the handler is callable, it is executed directly.
     * - If the handler is an array [ControllerClass::class, 'method'], the controller
     *   is resolved via the DI container and the given method is called.
     *
     * @param callable|array $handler Route handler to invoke
     *
     * @return void
     *
     * @throws RuntimeException If the controller class/method cannot be resolved
     */
    private function invoke(callable|array $handler): void
    {
        $response = null;

        if (is_callable($handler))
        {
            $response = $handler();
        }
        else if (is_array($handler) && count($handler) === 2)
        {
            [$class, $method] = $handler;

            if (!class_exists($class))
            {
                throw new RuntimeException("Controller class not found: $class");
            }
            
            $controller = $this->container->get($class);

            if (!method_exists($controller, $method))
            {
                throw new RuntimeException("Method not found: $method in $class");
            }

            $response = $controller->$method();
        }
        else
        {
            throw new RuntimeException('Invalid route handler format');
        }

        $this->sendJson($response);
    }

    /**
     * Writes a JSON response to the output buffer.
     *
     * Sets the JSON content type header (if headers are not already sent),
     * chooses an HTTP status code when appropriate, and encodes the provided
     * payload to JSON.
     *
     * Note:
     * If a handler/controller already set an HTTP status code via
     * http_response_code(), this method attempts to preserve it.
     *
     * @param mixed $data          Payload to encode as JSON
     * @param int   $defaultStatus Status code to use if no status has been set
     *
     * @return void
     */
    private function sendJson(mixed $data, int $defaultStatus = 200): void
    {
        if (!headers_sent())
        {
            header('Content-Type: application/json; charset=utf-8');

            if (http_response_code() === false || http_response_code() === HTTP_OK)
            {
                http_response_code($defaultStatus);
            }
        }

        try
        {
            echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        }
        catch (JsonException $e)
        {
            if (!headers_sent())
            {
                http_response_code(HTTP_INTERNAL_SERVER_ERROR);
            }
            echo json_encode(['error' => 'JSON Encoding Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Normalizes a request path.
     *
     * Removes trailing slashes and ensures that the root path is always "/".
     *
     * @param string $path Raw request path
     *
     * @return string Normalized path
     */
    private function normalize(string $path): string
    {
        $path = rtrim($path, '/');
        return (($path === '') ? '/' : $path);
    }

}