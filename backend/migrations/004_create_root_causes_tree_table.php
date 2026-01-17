<?php

$sql = "
    CREATE TABLE IF NOT EXISTS root_causes_tree (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        problem_id INT UNSIGNED NOT NULL,
        parent_id INT UNSIGNED NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (problem_id) REFERENCES problems(id),
        FOREIGN KEY (parent_id) REFERENCES root_causes_tree(id)
            ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
