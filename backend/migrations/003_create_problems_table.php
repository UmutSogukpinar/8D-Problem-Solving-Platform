<?php

$sql = "
    CREATE TABLE IF NOT EXISTS problems (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        created_by INT UNSIGNED NOT NULL,
        crew_id INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_problems_user
            FOREIGN KEY (created_by) REFERENCES users(id),
        CONSTRAINT fk_problems_crew
            FOREIGN KEY (crew_id) REFERENCES crews(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
