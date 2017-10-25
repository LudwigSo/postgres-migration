<?php

namespace LudwigBr\postgres_migration\example\config;


use LudwigBr\postgres_migration\config\DatabaseInterface;


class Database implements DatabaseInterface
{
    public function getDsn() :string
    {
        return $this->getDriver().":host=".$this->getHost().";port=".$this->getPort().";dbname=".$this->getDbName().";user=".$this->getUser().";password=".$this->getPassword();
    }

    public function getDriver() :string
    {
        return 'pgsql';
    }

    public function getHost() :string
    {
        return 'localhost';
    }

    public function getPort() :int
    {
        return 5432;
    }

    public function getDbName() :string
    {
        return 'woh';
    }

    public function getPgDbName() :string
    {
        return 'postgres';
    }

    public function getUser() :string
    {
        return 'postgres';
    }

    public function getPassword() :string
    {
        return 'postgres';
    }

}