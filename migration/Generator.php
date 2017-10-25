<?php

namespace LudwigBr\postgres_migration\migration;


use LudwigBr\postgres_migration\config\DatabaseInterface;
use LudwigBr\postgres_migration\config\DirectoriesInterface;


/**
 * Class Generator
 * @package LudwigBr\DatabaseMigration\Postgres
 */
class Generator
{
    /**
     * @var \PDO
     */
    protected $pdo;
    /**
     * @var DatabaseInterface
     */
    protected $databaseConfig;
    /**
     * @var DirectoriesInterface
     */
    protected $directoriesConfig;

    /**
     * Generator constructor.
     * @param DatabaseInterface $databaseConfig
     * @param DirectoriesInterface $directoriesConfig
     * @param \PDO $pdo
     */
    public function __construct(DatabaseInterface $databaseConfig, DirectoriesInterface $directoriesConfig, \PDO $pdo)
    {
        $this->databaseConfig = $databaseConfig;
        $this->directoriesConfig = $directoriesConfig;
        $this->pdo = $pdo;
    }

    /**
     * @param bool $withData
     */
    public function generateFiles(bool $withData = false)
    {
        $addConstraints = fopen($this->directoriesConfig->getPathToConstraints().'/add.sql', 'w');
        $dropConstraints = fopen($this->directoriesConfig->getPathToConstraints().'/drop.sql', 'w');
        $this->writeAllButCompositeForeignKeys($this->selectAllButCompositeForeignKeys(), $addConstraints, $dropConstraints);
        $this->writeCompositeForeignKeys($this->selectOnlyCompositeForeignKeys(), $addConstraints, $dropConstraints);
        fclose($addConstraints);
        fclose($dropConstraints);

        $this->writeSchemaAndData($this->selectTableNames(), $this->directoriesConfig->getPathToSchema(), $this->directoriesConfig->getPathToData(), $withData);
    }

    /**
     * @return \PDOStatement
     */
    protected function selectOnlyCompositeForeignKeys()
    {
        return $this->selectForeignKeysHelper(true);
    }

    /**
     * @return \PDOStatement
     */
    protected function selectAllButCompositeForeignKeys()
    {
        return $this->selectForeignKeysHelper(false);
    }

    /**
     * @param bool $composite
     * @return \PDOStatement
     */
    protected function selectForeignKeysHelper (bool $composite = false)
    {
        $compareOperator = ' = ';
        if ($composite) {
            $compareOperator = ' > ';
        }

        $query =
            "SELECT
          tc.constraint_name,
          tc.constraint_type,
          tc.table_name,
          kcu.column_name,
          tc.is_deferrable,
          tc.initially_deferred,
          rc.match_option AS match_type,
        
          rc.update_rule AS on_update,
          rc.delete_rule AS on_delete,
          ccu.table_name AS references_table,
          ccu.column_name AS references_field
        
        FROM information_schema.table_constraints tc
        
        LEFT JOIN information_schema.key_column_usage kcu
          ON tc.constraint_catalog = kcu.constraint_catalog
          AND tc.constraint_schema = kcu.constraint_schema
          AND tc.constraint_name = kcu.constraint_name
        
        LEFT JOIN information_schema.referential_constraints rc
          ON tc.constraint_catalog = rc.constraint_catalog
          AND tc.constraint_schema = rc.constraint_schema
          AND tc.constraint_name = rc.constraint_name
        
        LEFT JOIN information_schema.constraint_column_usage ccu
          ON rc.unique_constraint_catalog = ccu.constraint_catalog
          AND rc.unique_constraint_schema = ccu.constraint_schema
        AND rc.unique_constraint_name = ccu.constraint_name
        
        WHERE
            tc.constraint_type = 'FOREIGN KEY'
            AND tc.constraint_name IN (
              SELECT tc2.constraint_name
              FROM information_schema.table_constraints tc2
                LEFT JOIN information_schema.key_column_usage kcu2
                  ON kcu2.constraint_name = tc2.constraint_name
                  AND tc2.constraint_schema = kcu2.constraint_schema
                  AND tc2.constraint_name = kcu2.constraint_name
              WHERE tc2.constraint_type = 'FOREIGN KEY'
              GROUP BY tc2.constraint_name
              HAVING COUNT(tc2.constraint_name) $compareOperator 1
    )";

        return $this->pdo->query($query);
    }

    /**
     * @param \PDOStatement $fks
     * @param resource $addFile
     * @param resource $dropFile
     */
    protected function writeAllButCompositeForeignKeys(\PDOStatement $fks, $addFile, $dropFile) {
        while($obj = $fks->fetchObject()) {
            $drop_constraint = 'ALTER TABLE ONLY '.$obj->table_name.' DROP CONSTRAINT IF EXISTS "'.$obj->constraint_name.'" CASCADE;';
            fwrite($dropFile, $drop_constraint.PHP_EOL);

            $add_constraint = 'ALTER TABLE ONLY '.$obj->table_name.' ADD CONSTRAINT "'.$obj->constraint_name.'" FOREIGN KEY ('.$obj->column_name.') REFERENCES '.$obj->references_table.'('.$obj->references_field.') ON UPDATE '.$obj->on_update.  ' ON DELETE '.$obj->on_delete.';';
            fwrite($addFile, $add_constraint.PHP_EOL);
        }
    }

    /**
     * @param \PDOStatement $fks
     * @param resource $addFile
     * @param resource $dropFile
     */
    protected function writeCompositeForeignKeys(\PDOStatement $fks, $addFile, $dropFile) {
        $compositeFKs = $this->restructureCompositeForeignKeyStatement($fks);
        foreach ($compositeFKs as $compositeFK) {
            $drop_constraint = $drop_constraint = 'ALTER TABLE ONLY '.$compositeFK['table_name'].' DROP CONSTRAINT IF EXISTS "'.$compositeFK['constraint_name'].'" CASCADE;';
            fwrite($dropFile, $drop_constraint.PHP_EOL);

            $add_constraint = 'ALTER TABLE ONLY '.$compositeFK['table_name'].' ADD CONSTRAINT "'.$compositeFK['constraint_name'].'" FOREIGN KEY ('.implode(', ', $compositeFK['column_names']).') REFERENCES '.$compositeFK['references_table'].'('.implode(', ', $compositeFK['references_fields']).') ON UPDATE '.$compositeFK['on_update'].' ON DELETE '.$compositeFK['on_delete'].';';
            fwrite($addFile, $add_constraint.PHP_EOL);
        }
    }

    /**
     * @param \PDOStatement $fks
     * @return array
     */
    protected function restructureCompositeForeignKeyStatement(\PDOStatement $fks) {
        $compositeFKs = [];
        $lastCompositeKeyName = null;
        while($obj = $fks->fetchObject()) {
            if($lastCompositeKeyName != $obj->constraint_name) {
                array_push($compositeFKs,[
                    'constraint_name' => $obj->constraint_name,
                    'table_name' => $obj->table_name,
                    'column_names' => [$obj->column_name],
                    'on_update' => $obj->on_update,
                    'on_delete' => $obj->on_delete,
                    'references_table' => $obj->references_table,
                    'references_fields' => [$obj->references_field]
                ]);
            } else {
                if(!in_array($obj->column_name, $compositeFKs[count($compositeFKs)-1]['column_names'])) {
                    array_push($compositeFKs[count($compositeFKs)-1]['column_names'], $obj->column_name);
                }
                if(!in_array($obj->references_field, $compositeFKs[count($compositeFKs)-1]['references_fields'])) {
                    array_push($compositeFKs[count($compositeFKs)-1]['references_fields'], $obj->references_field);
                }
            }
            $lastCompositeKeyName = $obj->constraint_name;
        }
        return $compositeFKs;
    }

    /**
     * @param \PDOStatement $tableNames
     * @param string $pathToSchemaFile
     * @param string $pathToDataFile
     * @param bool $withData
     */
    protected function writeSchemaAndData(\PDOStatement $tableNames, string $pathToSchemaFile, string $pathToDataFile, bool $withData = true) {
        while($obj = $tableNames->fetchObject()) {
            $table = $obj->table_name;
            exec("sudo pg_dump -d ".$this->databaseConfig->getDbName()." --username=".$this->databaseConfig->getUser()." --schema-only --table=$table > $pathToSchemaFile/$table.sql");
            if($withData) {
                exec("sudo pg_dump -d ".$this->databaseConfig->getDbName()." --username=".$this->databaseConfig->getUser()." --data-only --table=$table > $pathToDataFile/$table.sql");
            }
        }
    }

    /**
     * @return \PDOStatement
     */
    protected function selectTableNames() {
        return $this->pdo->query("SELECT table_name
              FROM information_schema.tables
             WHERE table_schema='public'
               AND table_type='BASE TABLE';");
    }

    protected function dropForeignKeys()
    {
        exec("sudo psql --dbname=".$this->databaseConfig->getDbName()." --username=".$this->databaseConfig->getUser()." < ".$this->directoriesConfig->getPathToConstraints()."/drop.sql");
    }

    protected function addForeignKeys()
    {
        exec("sudo psql --dbname=".$this->databaseConfig->getDbName()." --username=".$this->databaseConfig->getUser()." < ".$this->directoriesConfig->getPathToConstraints()."/add.sql");
    }
}