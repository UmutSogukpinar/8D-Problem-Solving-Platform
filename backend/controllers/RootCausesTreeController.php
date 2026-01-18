<?php

declare(strict_types=1);

class RootCausesTreeController extends AbstractController
{
    public function __construct(private RootCausesTreeService $service) {}

    public function get(int $id): void
    {
        $nodeCause = $this->service->getById($id);

        if ($nodeCause === null)
        {
            $this->toJson(
                ['error' => 'Resource not found'],
                HTTP_NOT_FOUND
            );
            return ;
        }

        $this->toJson(
            $nodeCause,
            HTTP_OK
        );
    }

    public function store(): void
    {
        $data = $this->readJsonBody();

        if ($data === null)
        {
            $this->toJson(['error' => 'Invalid JSON body'], HTTP_BAD_REQUEST);
            return ;
        }

        
    }
}