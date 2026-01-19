<?php

declare(strict_types=1);

final class Router
{
    /**
     * Registered routes indexed by HTTP method and normalized path.
     *
     * @var array<string, array<string, callable|array|string>>
     */
    private array $routes = [];

    /**
     * Registers a GET route.
     *
     * @param string                 $path    Request path (e.g. "/health")
     * @param callable|array|string  $handler Handler to execute when the route is matched
     *
     * @return void
     */
    public function get(string $path, callable|array|string $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    /**
     * Dispatches the current HTTP request to the matched route handler.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = $this->normalize(
            parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'
        );

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null)
        {
            http_response_code(HTTP_NOT_FOUND);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Route not found'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $this->invoke($handler);
    }

    /**
     * Invokes a route handler.
     *
     * Supported handler formats:
     *  - callable
     *  - [ControllerClass::class, 'method']
     *  - 'Controller@method'
     *
     * @param callable|array|string $handler Registered route handler
     *
     * @return void
     */
    private function invoke(callable|array|string $handler): void
    {
        if (is_callable($handler))
        {
            $handler();
            return;
        }

        if (is_array($handler) && count($handler) === 2)
        {
            [$class, $method] = $handler;

            if (!is_string($class) || !is_string($method))
            {
                throw new RuntimeException('Invalid array handler');
            }

            $controller = new $class();
            $controller->$method();
            return;
        }

        if (is_string($handler) && strpos($handler, '@') !== false)
        {
            [$class, $method] = explode('@', $handler, 2);

            $controller = new $class();
            $controller->$method();
            return;
        }

        throw new RuntimeException('Invalid route handler');
    }

    /**
     * Normalizes a request path.
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
