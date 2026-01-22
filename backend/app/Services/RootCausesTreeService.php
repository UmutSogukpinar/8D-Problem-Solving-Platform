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
     * This method is responsible for creating a new node in the root cause tree structure. Each node is associated with a specific problem and can optionally have a parent node, forming a hierarchical tree.
     *
     * @param int      $problemId   The ID of the problem this node is associated with.
     * @param int|null $parentId    The ID of the parent node. Use null for root-level nodes.
     * @param string   $description A brief description of the root cause represented by this node.
     * @param int      $authorId    The ID of the user who created this node.
     *
     * @return int The ID of the newly created root cause node.
     */
    public function create(
        int $problemId,
        ?int $parentId,
        string $description,
        int $authorId
    ): int 
    {
        if ($parentId === 0) 
        {
            $parentId = null;
        }

        return ($this->repository->create(
                    $problemId,
                    $parentId,
                    $description,
                    $authorId
            )
        );
    }

    /**
     * Retrieves a root cause tree node along with its related problem details.
     *
     * @param int $id ID of the root cause tree node
     *
     * @return array{
     *     problem: array{
     *         id: int,
     *         title: string,
     *         description: string,
     *         createdAt: string,
     *         author: array{id:int, name:string},
     *         crew: array{id:int, name:string}
     *     },
     *     node: array{
     *         id: int,
     *         parentId: int|null,
     *         description: string,
     *         createdAt: string
     *     }
     * }|null
     */
    public function getById(int $id): ?array
        {
            $row = $this->repository->findById($id);

            if (!$row) 
            {
                return (null);
            }

            $nodes = $this->mapRawNodes([$row]);

            return (
                [
                    'problem' => $this->extractProblemDetails($row),
                    'node'    => $nodes[0]
                ]
            );
        }

    /**
     * Returns the problem details and its root cause tree structure.
     *
     * @param int $problemId ID of the problem
     *
     * @return array{
     *     problem: array|null,
     *     tree: array
     * }
     */
    public function getTreeByProblemId(int $problemId): ?array
    {
            $rawList = $this->repository->findTreeByProblemId($problemId);

            if (empty($rawList)) 
            {
                return (null);
            }

            $firstRow = current($rawList);
            $problem = $this->extractProblemDetails($firstRow);

            $hasNodes = !empty($firstRow['id']);
            
            $problem['nodes'] = $hasNodes 
            ? $this->sortNodesHierarchically($this->mapRawNodes($rawList)) 
            : [];

            return ($problem);
    }

    /**
     * Extracts problem-level details from a raw database row.
     *
     * @param array $row Raw database row containing problem data
     *
     * @return array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     createdAt: string,
     *     author: array{id:int, name:string},
     *     crew: array{id:int, name:string}
     * }
     */
    private function extractProblemDetails(array $row): array
    {
        return (
            [
                'id'          => $row['problem_id'],
                'title'       => $row['problem_title'],
                'description' => $row['problem_description'],
                'createdAt'   => $row['problem_created_at'],
                'author'      => [
                    'id'   => $row['created_by_id'],
                    'name' => $row['created_by_name'],
                ],
                'crew'        => [
                    'id'   => $row['crew_id'],
                    'name' => $row['crew_name'],
                ]
            ]
        );
    }

    /**
     * Maps raw database rows into a normalized node list
     * suitable for building a hierarchical tree.
     *
     * @param array $rawList Flat list of raw node rows
     *
     * @return array<int, array{
     *     id: int,
     *     parentId: int|null,
     *     description: string,
     *     authorId: int
     *     authorName: string
     *     createdAt: string
     * }>
     */
    private function mapRawNodes(array $rawList): array
    {
        return (
            array_map(
                function (array $row): array
                {
                    return (
                        [
                            'id'          => $row['id'],
                            'parentId'    => $row['parent_id'],
                            'description' => $row['description'],
                            'authorId'    => $row['author_id'],
                            'authorName'  => $row['author_name'],
                            'createdAt'   => $row['created_at']
                        ]
                    );
                },
                $rawList
            )
        );
    }

    /**
     * Orders a flat list of nodes so that each parent node
     * is immediately followed by its descendants.
     *
     * @param array<int, array{
     *     id: int,
     *     parentId: int|null,
     *     description: string,
     *     createdAt: string
     * }> $elements Flat list of mapped nodes
     *
     * @return array<int, array{
     *     id: int,
     *     parentId: int|null,
     *     description: string,
     *     createdAt: string
     * }>
     */
    private function sortNodesHierarchically(array $elements): array
    {
        $groupedByParent = [];

        foreach ($elements as $element) 
        {
            $key = $element['parentId'] ?? 'root';
            $groupedByParent[$key][] = $element;
        }

        return ($this->flattenTree($groupedByParent, null));
    }

    /**
     * Flattens a parent-grouped node structure into a single
     * depth-first ordered list
     *
     * @param array<string|int, array<int, array{
     *     id: int,
     *     parentId: int|null,
     *     description: string,
     *     createdAt: string
     * }>> $groupedNodes Nodes grouped by parentId
     *
     * @param int|null $parentId Current parent node ID
     *
     * @return array<int, array{
     *     id: int,
     *     parentId: int|null,
     *     description: string,
     *     createdAt: string
     * }>
     */
    private function flattenTree(array $groupedNodes, ?int $parentId): array
    {
        $key = $parentId ?? 'root';

        if (!isset($groupedNodes[$key])) 
        {
            return ([]);
        }

        $result = [];

        foreach ($groupedNodes[$key] as $node) 
        {
            $result[] = $node;

            $children = $this->flattenTree($groupedNodes, $node['id']);

            if (!empty($children)) 
            {
                $result = array_merge($result, $children);
            }
        }

        return ($result);
    }

}
