<?php

namespace LudwigBr\postgres_migration\config;


interface DatabaseInterface
{
    public function getDsn() :string ;
    public function getDriver()  :string ;
    public function getHost() :string ;
    public function getPort() :int ;
    public function getDbName() :string ;
    public function getPgDbName(): string ;
    public function getUser() :string ;
    public function getPassword() :string ;
}