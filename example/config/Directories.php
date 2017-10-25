<?php

namespace LudwigBr\postgres_migration\example\config;


use LudwigBr\postgres_migration\config\DirectoriesInterface;


class Directories implements DirectoriesInterface
{
    public function getPathToSchema() :string
    {
        return __DIR__.'/../result/schema';
    }

    public function getPathToData() :string
    {
        return __DIR__.'/../result/data';
    }

    public function getPathToConstraints() :string
    {
        return __DIR__.'/../result/constraints';
    }

}