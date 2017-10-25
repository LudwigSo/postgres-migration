<?php

namespace LudwigBr\postgres_migration\example\config;


use LudwigBr\postgres_migration\config\DatabaseInterface;


/**
 * Class Database
 * @package LudwigBr\postgres_migration\example\config
 */
class Database implements DatabaseInterface
{
    /**
     * @return string
     */
    public function getDsn() :string
    {
        return $this->getDriver().":host=".$this->getHost().";port=".$this->getPort().";dbname=".$this->getDbName().";user=".$this->getUser().";password=".$this->getPassword();
    }

    /**
     * @return string
     */
    public function getDriver() :string
    {
        return 'pgsql';
    }

    /**
     * @return string
     */
    public function getHost() :string
    {
        return 'localhost';
    }

    /**
     * @return int
     */
    public function getPort() :int
    {
        return 5432;
    }

    /**
     * @return string
     */
    public function getDbName() :string
    {
        return 'woh';
    }

    /**
     * @return string
     */
    public function getPgDbName() :string
    {
        return 'postgres';
    }

    /**
     * @return string
     */
    public function getUser() :string
    {
        return 'postgres';
    }

    /**
     * @return string
     */
    public function getPassword() :string
    {
        return 'postgres';
    }

}