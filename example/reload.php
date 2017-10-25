<?php

/*
 * includes all needed files and 2 variables:
 * $dbConfig
 * $dirConfig
 */
require_once __DIR__.'/config/includes.php';

$reloader = new \LudwigBr\postgres_migration\migration\Reloader(
    $dbConfig,
    $dirConfig
);

$reloader->reload(true);