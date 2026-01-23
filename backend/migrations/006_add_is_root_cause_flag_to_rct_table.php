<?php

$sql = "
    ALTER TABLE root_causes_tree
    ADD COLUMN is_root_cause BOOLEAN NOT NULL DEFAULT FALSE;
";