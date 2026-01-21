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
    public function getTreeByProblemId(int $problemId): array
    {
        $rawList = $this->repository->findTreeByProblemId($problemId);

        if (empty($rawList))
        {
            return (
                [
                    'problem' => null,
                    'tree'    => []
                ]
            );
        }

        return (
            [
                'problem' => $this->extractProblemDetails($rawList[0]),
                'tree'    => $this->buildTree(
                    $this->mapRawNodes($rawList)
                )
            ]
        );
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
                            'createdAt'   => $row['created_at'],
                        ]
                    );
                },
                $rawList
            )
        );
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
        $tree = [];
        $nodesById = [];

        foreach ($elements as $element)
        {
            $element['children'] = [];
            $nodesById[$element['id']] = $element;
        }

        foreach ($nodesById as $id => &$node) 
        {
            $parentId = $node['parentId'];

            if ($parentId === null) 
            {
                $tree[] = &$node;
            }
            else
            {
                if (isset($nodesById[$parentId]))
                {
                    $nodesById[$parentId]['children'][] = &$node;
                }
            }
        }

        return ($tree);
    }

}
