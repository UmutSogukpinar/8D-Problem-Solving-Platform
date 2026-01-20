<?php

declare(strict_types=1);

use PDO;

return function (PDO $pdo): void {
    $problemId = (int)$pdo->query(
        "
        SELECT id FROM problems
        WHERE title='Login fails intermittently'
        ORDER BY id DESC
        LIMIT 1"
    )->fetchColumn();

    if ($problemId <= 0) {
        throw new RuntimeException("Problem not found for root causes seed");
    }

    // Root node
    $rootDesc = 'Authentication service unstable';

    $pdo->prepare(
        "
        INSERT INTO root_causes_tree (problem_id, parent_id, description)
        SELECT :problem_id, NULL, :description
        WHERE NOT EXISTS (
            SELECT 1 FROM root_causes_tree
            WHERE problem_id = :problem_id
              AND parent_id IS NULL
              AND description = :description
        )
    "
    )->execute([
        'problem_id' => $problemId,
        'description' => $rootDesc,
    ]);

    $rootId = (int)$pdo->prepare(
        "
        SELECT id FROM root_causes_tree
        WHERE problem_id = :problem_id
          AND parent_id IS NULL
          AND description = :description
        ORDER BY id DESC
        LIMIT 1
    "
    )->execute(['problem_id' => $problemId, 'description' => $rootDesc]) ?: 0;

    // PDO quirk: fetch after execute
    $stmtRoot = $pdo->prepare("
        SELECT id FROM root_causes_tree
        WHERE problem_id = :problem_id
          AND parent_id IS NULL
          AND description = :description
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmtRoot->execute(['problem_id' => $problemId, 'description' => $rootDesc]);
    $rootId = (int)$stmtRoot->fetchColumn();

    // Children
    $children = [
        'DB connection pool exhausted',
        'Cache stampede on login endpoint',
    ];

    $stmtChild = $pdo->prepare("
        INSERT INTO root_causes_tree (problem_id, parent_id, description)
        SELECT :problem_id, :parent_id, :description
        WHERE NOT EXISTS (
            SELECT 1 FROM root_causes_tree
            WHERE problem_id = :problem_id
              AND parent_id = :parent_id
              AND description = :description
        )
    ");

    foreach ($children as $desc) {
        $stmtChild->execute([
            'problem_id' => $problemId,
            'parent_id' => $rootId,
            'description' => $desc,
        ]);
    }
};
