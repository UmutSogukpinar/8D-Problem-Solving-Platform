<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    /**
     * Explicit bindings for services.
     *
     * Maps an identifier (usually a class name) to a factory function
     * that knows how to create the corresponding object.
     *
     * @var array<string, callable(self): object>
     */
    private array $bindings = [];

    /**
     * Cached instances for the current request.
     *
     * Ensures that each service is instantiated only once per request.
     *
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Binds a service identifier to a factory function.
     *
     * The factory receives the container itself, allowing nested
     * dependency resolution.
     *
     * @param string   $id       Service identifier (usually a class name)
     * @param callable $factory  Factory function returning the service instance
     *
     * @return void
     */
    public function bind(string $id, callable $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    /**
     * Retrieves an instance of the given service identifier.
     *
     * Resolution order:
     *  1. Return cached instance if available
     *  2. Create via explicit binding if defined
     *  3. Attempt automatic resolution via reflection (autowiring)
     *
     * @param string $id Service identifier (class name)
     *
     * @return object Resolved service instance
     *
     * @throws RuntimeException If the service cannot be resolved
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id]))
        {
            return $this->instances[$id];
        }

        if (isset($this->bindings[$id]))
        {
            $obj = ($this->bindings[$id])($this);
            $this->instances[$id] = $obj;
            return ($obj);
        }

        $obj = $this->autowire($id);
        $this->instances[$id] = $obj;

        return ($obj);
    }

    /**
     * Automatically resolves and instantiates a class using reflection.
     *
     * Constructor dependencies are resolved recursively based on type hints.
     * Only class-based (non-builtin) parameters are supported.
     *
     * @param string $class Fully qualified class name
     *
     * @return object Instantiated class with resolved dependencies
     *
     * @throws RuntimeException If the class or its dependencies cannot be resolved
     */
    private function autowire(string $class): object
    {
        if (!class_exists($class))
        {
            throw new RuntimeException("Class not found: {$class}");
        }

        $ref = new ReflectionClass($class);

        if (!$ref->isInstantiable())
        {
            throw new RuntimeException("Class not instantiable: {$class}");
        }

        $ctor = $ref->getConstructor();

        if ($ctor === null || $ctor->getNumberOfParameters() === 0)
        {
            return (new $class());
        }

        $args = [];

        foreach ($ctor->getParameters() as $param)
        {
            $type = $param->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin())
            {
                if ($param->isDefaultValueAvailable())
                {
                    $args[] = $param->getDefaultValue();
                    continue;
                }

                throw new RuntimeException(
                    "Cannot resolve parameter \${$param->getName()} for {$class}"
                );
            }

            $args[] = $this->get($type->getName());
        }

        return ($ref->newInstanceArgs($args));
    }
}
