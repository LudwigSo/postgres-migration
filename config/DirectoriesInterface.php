<?php

namespace LudwigBr\postgres_migration\config;


interface DirectoriesInterface
{
    public function getPathToSchema() :string ;
    public function getPathToData() :string ;
    public function getPathToConstraints() :string ;
}