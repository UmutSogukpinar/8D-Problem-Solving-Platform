<?php

declare(strict_types=1);

class RootCausesTreeController extends AbstractController
{
    public function __construct(private RootCausesTreeService $service) {}

    public function getRootCause(int $id): void
    {
        $this->get($id, [$this->service, 'getById']);
    }

    public function store(): void
    {
        
    }

}