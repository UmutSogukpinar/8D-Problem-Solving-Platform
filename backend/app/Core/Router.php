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
     * Registers a POST route.
     *
     * @param string         $path    Request path (e.g. "/problems")
     * @param callable|array $handler Route handler
     *
     * @return void
     */
    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    /**
     * Registers a PATCH route.
     *
     * @param string         $path    Request path (e.g. "/problems")
     * @param callable|array $handler Route handler
     *
     * @return void
     */
    public function patch(string $path, callable|array $handler): void
    {
        $this->routes['PATCH'][$this->normalize($path)] = $handler;
    }

    /**
     * Dispatches the current HTTP request to a registered route.
     *
     * Reads the HTTP method and request URI from the server environment,
     * normalizes the path (query string is ignored), then attempts to resolve
     * a matching route handler in this order:
     *
     * 1) Exact match (static routes), e.g. "/health"
     * 2) Parameterized match (dynamic routes), e.g. "/users/{id}"
     *    - "{param}" segments are treated as single path parts and matched as "([^/]+)"
     *    - Parameter names are not used; only their order is used
     *
     * @throws RuntimeException If a matched route handler is invalid or cannot be invoked
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = $this->normalize(
            parse_url($_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH) ?: '/'
        );

        if (isset($this->routes[$method][$path]))
        {
            $handler = $this->routes[$method][$path];
            $this->invoke($handler, []);
            return;
        }

        if (isset($this->routes[$method]))
        {
            foreach ($this->routes[$method] as $routePath => $handler) 
            {
                if (strpos($routePath, '{') === false)
                {
                    continue;
                }

                $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath);
                $pattern = "~^" . $pattern . "$~";

                if (preg_match($pattern, $path, $matches))
                {
                    array_shift($matches);
                    $this->invoke($handler, $matches); 
                    return ;
                }
            }
        }
        $this->sendJson(['error' => 'Route not found'], 404);
    }

    /**
     * Invokes a resolved route handler and outputs its return value as JSON.
     *
     * Supported handler formats:
     * - callable (function/closure)
     * - array{0: class-string, 1: string} (e.g. [ControllerClass::class, 'method'])
     *
     * For controller handlers, the controller instance is resolved through the DI container.
     * Route variables (from dynamic routes) are forwarded as positional arguments.
     *
     * @param callable|array{0: class-string, 1: string} $handler The route handler to invoke
     * @param array<int, string> $vars Positional route variables extracted from the path
     *
     * @throws RuntimeException If the handler format is invalid, the controller cannot be resolved,
     *                          or the target method does not exist
     *
     * @return void
     */
    private function invoke(callable|array $handler, array $vars = []): void
    {
        $response = null;

        if (is_callable($handler) && !is_array($handler))
        {
            $response = call_user_func_array($handler, $vars);
        }
        else if (is_array($handler) && count($handler) === 2)
        {
            [$class, $method] = $handler;

            try {
                $controller = $this->container->get($class);
            } catch (RuntimeException $e) {
                throw new RuntimeException("Controller instantiation failed: " . $e->getMessage());
            }

            if (!method_exists($controller, $method))
            {
                throw new RuntimeException("Method not found: {$method} in {$class}");
            }

            $response = call_user_func_array([$controller, $method], $vars);
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