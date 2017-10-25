<?php

namespace LudwigBr\postgres_migration\migration;


use LudwigBr\postgres_migration\config\DatabaseInterface;
use LudwigBr\postgres_migration\config\DirectoriesInterface;


/**
 * Class Reloader
 * @package LudwigBr\postgres\migration
 */
class Reloader
{
    /**
     * @var DatabaseInterface
     */
    protected $databaseConfig;
    /**
     * @var DirectoriesInterface
     */
    protected $directoriesConfig;

    /**
     * Reloader constructor.
     * @param DatabaseInterface $databaseConfig
     * @param DirectoriesInterface $directoriesConfig
     */
    public function __construct(DatabaseInterface $databaseConfig, DirectoriesInterface $directoriesConfig)
    {
        $this->databaseConfig = $databaseConfig;
        $this->directoriesConfig = $directoriesConfig;
    }

    /**
     * @param bool $withData
     */
    public function reload($withData = false)
    {
        $this->dropDatabase();
        $this->createDatabase();
        $this->addSchema();
        if($withData) {
            $this->addData();
        }
        $this->addForeignKeys();
    }

    /**
     *
     */
    protected function dropDatabase()
    {
        $dropCommands = [
            "UPDATE pg_database SET datallowconn = 'false' WHERE datname = '".$this->databaseConfig->getDbName()."';",
            "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '".$this->databaseConfig->getDbName()."';",
            "DROP DATABASE ".$this->databaseConfig->getDbName().";"
        ];
        foreach($dropCommands as $dropCommand) {
            exec("sudo psql --dbname=".$this->databaseConfig->getPgDbName()." --username=".$this->databaseConfig->getUser()." --command=\"$dropCommand\"");
        }   
    }

    /**
     *
     */
    protected function createDatabase()
    {
        $createCommand = "CREATE DATABASE ".$this->databaseConfig->getDbName().";";
        exec("sudo psql --dbname=".$this->databaseConfig->getPgDbName()." --username=".$this->databaseConfig->getUser()." --command=\"$createCommand\"");
    }

    /**
     *
     */
    protected function addSchema()
    {
        $schema_files = scandir($this->directoriesConfig->getPathToSchema());
        $this->addGenericFiles($schema_files, $this->directoriesConfig->getPathToSchema());
    }

    /**
     *
     */
    protected function addData()
    {
        $data_files = scandir($this->directoriesConfig->getPathToData());
        $this->addGenericFiles($data_files, $this->directoriesConfig->getPathToData());
    }

    /**
     * @param string[] $files
     * @param string $path
     */
    protected function addGenericFiles($files, $path)
    {
        foreach ($files as $file) {
            if(!is_file("$path/$file")) {
                break;
            }
            exec("sudo psql --dbname=".$this->databaseConfig->getDbName()." --username=".$this->databaseConfig->getUser()." < $path/$file");
        }
    }

    /**
     *
     */
    protected function addForeignKeys()
    {
        exec("sudo psql --dbname=".$this->databaseConfig->getDbName()." --username=".$this->databaseConfig->getUser()." < ".$this->directoriesConfig->getPathToConstraints()."/add.sql");
    }
    
}