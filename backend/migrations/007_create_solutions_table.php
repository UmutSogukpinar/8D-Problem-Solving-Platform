<?php

$sql = 
"
    CREATE TABLE IF NOT EXISTS solutions (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        problem_id INT UNSIGNED NOT NULL,
        root_cause_id INT UNSIGNED NOT NULL,
        author_id INT UNSIGNED NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
        FOREIGN KEY (root_cause_id) REFERENCES root_causes_tree(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";