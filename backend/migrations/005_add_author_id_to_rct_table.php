<?php

$sql = "
    ALTER TABLE root_causes_tree
    ADD COLUMN author_id INT UNSIGNED NOT NULL AFTER description,
    ADD CONSTRAINT fk_root_causes_tree_author
        FOREIGN KEY (author_id) REFERENCES users(id)
        ON DELETE CASCADE;
";