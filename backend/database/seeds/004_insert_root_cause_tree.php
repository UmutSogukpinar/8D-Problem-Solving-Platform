<?php

declare(strict_types=1);

return function (PDO $pdo): void
{
    $problemTitle = 'Login fails intermittently';
    
    $stmtProb = $pdo->prepare("SELECT id FROM problems WHERE title = :title ORDER BY id DESC LIMIT 1");
    $stmtProb->execute(['title' => $problemTitle]);
    $problemId = $stmtProb->fetchColumn();

    if ($problemId === false)
    {
        logMessage(WARNING, "Problem '{$problemTitle}' not found, skipping seed 4.");
        return;
    }

    $problemId = (int)$problemId;

    $stmtUser = $pdo->prepare("SELECT id FROM users ORDER BY id ASC LIMIT 1");
    $stmtUser->execute();
    $authorId = $stmtUser->fetchColumn();

    if ($authorId === false) {
        logMessage(WARNING, "No user found to assign as author, skipping seed 4.");
        return;
    }
    $authorId = (int)$authorId;

    $rootDesc = 'Authentication service unstable';

    $stmtRoot = $pdo->prepare("
        INSERT INTO root_causes_tree (problem_id, parent_id, description, author_id)
        SELECT :pid, NULL, :desc, :author_id
        WHERE NOT EXISTS (
            SELECT 1 FROM root_causes_tree 
            WHERE problem_id = :c_pid AND parent_id IS NULL AND description = :c_desc
        )
    ");

    $stmtRoot->execute([
        'pid'       => $problemId,
        'desc'      => $rootDesc,
        'author_id' => $authorId,
        'c_pid'     => $problemId,
        'c_desc'    => $rootDesc
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
        INSERT INTO root_causes_tree (problem_id, parent_id, description, author_id)
        SELECT :pid, :parent_id, :desc, :author_id
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
            'author_id' => $authorId,
            'cpid'      => $problemId,
            'cparent'   => $rootId,
            'cdesc'     => $desc
        ]);
    }

    logMessage(INFO, "Seed 4 executed successfully!");
};