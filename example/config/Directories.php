<?php

namespace LudwigBr\postgres_migration\example\config;


use LudwigBr\postgres_migration\config\DirectoriesInterface;


/**
 * Class Directories
 *
 * ToDo:
 * If you don't use a custom config file please fill in your information into the following getMethods()!
 *
 * @package LudwigBr\postgres_migration\example\config
 */
class Directories implements DirectoriesInterface
{
    /**
     * @return string
     */
    public function getPathToSchema() :string
    {
        return __DIR__.'/../result/schema';
    }

    /**
     * @return string
     */
    public function getPathToData() :string
    {
        return __DIR__.'/../result/data';
    }

    /**
     * @return string
     */
    public function getPathToConstraints() :string
    {
        return __DIR__.'/../result/constraints';
    }

}