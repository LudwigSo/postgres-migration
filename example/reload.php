<?php

require __DIR__.'/config/includes.php';

$reloader = new \LudwigBr\postgres_migration\migration\Reloader(
    new \LudwigBr\postgres_migration\example\config\Database(),
    new \LudwigBr\postgres_migration\example\config\Directories()
);

$reloader->reload(true);