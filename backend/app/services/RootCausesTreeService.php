<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RootCausesTreeRepository;

class RootCausesTreeService
{
    public function __construct(private RootCausesTreeRepository $repository) {}

    /**
     * Creates a new root cause tree node.
     *
     * @param int      $problemId   ID of the related problem
     * @param int|null $parentId    Parent node ID (null for root-level nodes)
     * @param string   $description Description of the root cause
     *
     * @return int The ID of the newly created root cause node
     */
    public function create(
        int $problemId,
        ?int $parentId,
        string $description
    ): int
    {
        return ($this->repository->create(
            $problemId,
            $parentId,
            $description
        ));
    }

    /**
     * Retrieves the root cause tree for a specific problem.
     *
     * @param int $problemId The unique identifier of the problem.
     *
     * @return array Hierarchical array representing the root cause tree.
     */
    public function getTreeByProblemId(int $problemId): array
    {
        $rawMap = $this->repository->findTreeByProblemId($problemId);
        
        $nodeMap = $this->buildTree($rawMap);
        
        return ($nodeMap);
    }

    /**
     * Builds a hierarchical tree structure from a flat list of elements.
     *
     * @param array $elements Flat array of root cause nodes.
     *
     * @return array Hierarchical tree structure.
     */
    private function buildTree(array $elements): array
    {
        $branch = [];
        $keyed = [];

        foreach ($elements as $element) 
        {
            $element['children'] = [];
            $keyed[$element['id']] = $element;
        }

        foreach ($keyed as $id => &$node)
        {
            $parentId = $node['parent_id'];

            if ($parentId === null) 
            {
                $branch[] = &$node;
            } 
            else
            {
                if (isset($keyed[$parentId]))
                {
                    $keyed[$parentId]['children'][] = &$node;
                }
            }
        }

        return ($branch);
    }

    /**
     * Retrieves a root cause node by its unique identifier.
     *
     * @param int $id Root cause tree node ID
     *
     * @return array|null
     *         Associative array containing the root cause data,
     *         or null if the node does not exist.
     */
    public function getById(int $id): ?array
    {
        return ($this->repository->findById($id));
    }

}
