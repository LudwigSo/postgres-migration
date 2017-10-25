<?php

/*
 * ToDo
 * Change the paths to the files after you copied the files
 */
require_once __DIR__.'/../../config/DatabaseInterface.php';
require_once __DIR__.'/../../config/DirectoriesInterface.php';
require_once __DIR__.'/../../migration/Generator.php';
require_once __DIR__.'/../../migration/Reloader.php';
/*
 * ToDo
 * When you implemented the interfaces in other classes (NOT the ones in the example directory) you need to
 * change those paths aswell
 */
require_once __DIR__.'/Database.php';
require_once __DIR__.'/Directories.php';

/*
 * ToDo
 * When you implemented the interfaces in other classes (NOT the ones in the example directory) you need to
 * change the namespace to your config classes
 */
$dbConfig = new \LudwigBr\postgres_migration\example\config\Database();
$dirConfig = new \LudwigBr\postgres_migration\example\config\Directories();