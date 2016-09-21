<?php

$pdo = $db->connection;

$pdo->addColumn('CompletedMigration', 'file');
