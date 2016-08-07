<?php

$pdo = $db->connection;

$pdo->createEntity('CompletedMigration');
$pdo->addColumn('CompletedMigration', 'date', ['type' => 'datetime']);

$pdo->createEntity('_archive', false);
$pdo->addColumn('_archive', 'datetime', ['type' => 'datetime']);
$pdo->addColumn('_archive', 'entity');
$pdo->addColumn('_archive', 'data', ['type' => 'mediumtext']);
$pdo->removeColumn('_archive', 'id');


$pdo->createEntity('Page');
$pdo->addColumn('Page', 'name');
$pdo->addColumn('Page', 'url');
$pdo->addColumn('Page', 'class', ['default' => 'Fierce\PageController']);
$pdo->addColumn('Page', 'content', ['type' => 'mediumtext']);
$pdo->addColumn('Page', 'nav', ['null' => true]);
$pdo->addColumn('Page', 'navPositionLeft', ['type' => 'int']);
$pdo->addColumn('Page', 'navPositionRight', ['type' => 'int']);
$pdo->addColumn('Page', 'navPositionDepth', ['null' => true]);
$pdo->addColumn('Page', 'modifiedBy');
$pdo->addColumn('Page', 'modified', ['type' => 'datetime']);

$pdo->createEntity('NewsPost');
$pdo->addColumn('NewsPost', 'title');
$pdo->addColumn('NewsPost', 'date', ['type' => 'datetime']);
$pdo->addColumn('NewsPost', 'content', ['type' => 'mediumtext']);
$pdo->addColumn('NewsPost', 'modifiedBy');
$pdo->addColumn('NewsPost', 'modified', ['type' => 'datetime']);


$pdo->createEntity('User');
$pdo->addColumn('User', 'type');
$pdo->addColumn('User', 'name');
$pdo->addColumn('User', 'email');
$pdo->addColumn('User', 'password');
$pdo->addColumn('User', 'signature');
$pdo->addColumn('User', 'modifiedBy');
$pdo->addColumn('User', 'modified', ['type' => 'datetime']);


$pdo->createEntity('LoginFailure');
$pdo->addColumn('LoginFailure', 'failures', ['type' => 'text']);

$pdo->createEntity('LoginSession');
$pdo->addColumn('LoginSession', 'userId');
$pdo->addColumn('LoginSession', 'lastActive', ['type' => 'datetime']);
$pdo->addColumn('LoginSession', 'hash');
$pdo->addIndex('LoginSession', ['userId'], false);

$pdo->createEntity('ApiToken');
$pdo->addColumn('ApiToken', 'userId');
$pdo->addColumn('ApiToken', 'created', ['type' => 'datetime']);
$pdo->addColumn('ApiToken', 'hash');
$pdo->addIndex('ApiToken', ['userId'], false);

$pdo->createEntity('ScheduledTask');
$pdo->addColumn('ScheduledTask', 'status');
$pdo->addColumn('ScheduledTask', 'date', ['type' => 'datetime']);
$pdo->addColumn('ScheduledTask', 'repeat');
$pdo->addColumn('ScheduledTask', 'class');
$pdo->addColumn('ScheduledTask', 'method');
$pdo->addColumn('ScheduledTask', 'log', ['type' => 'mediumtext']);
