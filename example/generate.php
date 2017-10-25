<?php

require_once __DIR__.'/config/includes.php';

$dbConfig = new \LudwigBr\postgres_migration\example\config\Database();
$generator = new \LudwigBr\postgres_migration\migration\Generator(
    $dbConfig,
    new \LudwigBr\postgres_migration\example\config\Directories(),
    new PDO($dbConfig->getDsn())
);

$generator->generateFiles(true);