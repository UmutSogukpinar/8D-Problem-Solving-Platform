<?php

declare(strict_types=1);

use PDO;

return function (PDO $pdo): void
{
    $problemTitle = 'Login fails intermittently';
    
    $stmtProb = $pdo->prepare("SELECT id FROM problems WHERE title = :title ORDER BY id DESC LIMIT 1");
    $stmtProb->execute(['title' => $problemTitle]);
    $problemId = $stmtProb->fetchColumn();

    if ($problemId === false)
    {
        logMessage(WARNING, "Problem '{$problemTitle}' not found, skipping seed 4.");
        return ;
    }

    $problemId = (int)$problemId;
    $rootDesc = 'Authentication service unstable';

    $stmtRoot = $pdo->prepare("
        INSERT INTO root_causes_tree (problem_id, parent_id, description)
        SELECT :pid, NULL, :desc
        WHERE NOT EXISTS (
            SELECT 1 FROM root_causes_tree 
            WHERE problem_id = :c_pid AND parent_id IS NULL AND description = :c_desc
        )
    ");

    $stmtRoot->execute([
        'pid'    => $problemId,
        'desc'   => $rootDesc,
        'c_pid'  => $problemId,
        'c_desc' => $rootDesc
    ]);

    $rootId = (int)$pdo->lastInsertId();
    if ($rootId === 0) 
    {
        $stmtFindRoot = $pdo->prepare("SELECT id FROM root_causes_tree WHERE problem_id = :pid AND parent_id IS NULL AND description = :desc");
        $stmtFindRoot->execute(['pid' => $problemId, 'desc' => $rootDesc]);
        $rootId = (int)$stmtFindRoot->fetchColumn();
    }

    $children = [
        'DB connection pool exhausted',
        'Cache stampede on login endpoint',
    ];

    $stmtChild = $pdo->prepare("
        INSERT INTO root_causes_tree (problem_id, parent_id, description)
        SELECT :pid, :parent_id, :desc
        WHERE NOT EXISTS (
            SELECT 1 FROM root_causes_tree 
            WHERE problem_id = :cpid 
              AND parent_id = :cparent 
              AND description = :cdesc
        )
    ");

    foreach ($children as $desc) 
    {
        $stmtChild->execute([
            'pid'       => $problemId,
            'parent_id' => $rootId,
            'desc'      => $desc,
            'cpid'      => $problemId,
            'cparent'   => $rootId,
            'cdesc'     => $desc
        ]);
    }

    logMessage(INFO, "Seed 4 executed successfully!");
};
