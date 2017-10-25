<?php

/*
 * includes all needed files and 2 variables:
 * $dbConfig
 * $dirConfig
 */
require_once __DIR__.'/config/includes.php';

$generator = new \LudwigBr\postgres_migration\migration\Generator(
    $dbConfig,
    $dirConfig,
    new PDO($dbConfig->getDsn())
);

$generator->generateFiles(true);