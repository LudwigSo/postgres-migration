<?php

require_once __DIR__.'/config/includes.php';

$generator = new \LudwigBr\postgres_migration\migration\Generator(
    new \LudwigBr\postgres_migration\example\config\Database(),
    new \LudwigBr\postgres_migration\example\config\Directories()
);
$generator->generateFiles(true);