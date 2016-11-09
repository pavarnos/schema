<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Renderer;

use LSS\Schema\Table\Column;
use LSS\Schema;
use LSS\Schema\TableInterface;
use LSS\SchemaInterface;

/**
 * Class AlterTableSQL
 * @package LSS\Schema\Table\Renderer
 *
 * Take the Schema and return a string containing the ALTER TABLE statements necessary to update a database
 */
class AlterTableSQL
{
    const STATEMENT_SUFFIX = ";\n";

    /** @var array */
    private $ignoredTables = [ ];


    /**
     * @param array $ignoreTables
     */
    public function __construct($ignoreTables = [ ])
    {
        $this->ignoredTables = $ignoreTables;
    }


    /**
     * return a set of sql statements that turn $copy into $master
     * @param SchemaInterface $master
     * @param SchemaInterface $copy
     * @return string[] sql statements to execute
     */
    public function render(SchemaInterface $master, SchemaInterface $copy)
    {
        $sql = array();

        $masterTables = $this->getTableNames($master);
        $copyTables   = $this->getTableNames($copy);

        // all tables not in $copy will be created
        foreach (array_diff($masterTables, $copyTables) as $tableName) {
            $sql[] = $master->getTable($tableName)->toSQL();
        }

        // all tables not in $master will be dropped
        foreach (array_diff($copyTables, $masterTables) as $tableName) {
            $sql[] = 'drop table ' . Schema::quoteIdentifier($tableName);
        }

        // tables in both will be compared field by field, index by index
        foreach (array_intersect($masterTables, $copyTables) as $tableName) {
            $sql = array_merge($sql,
                $this->compareTableColumns($master->getTable($tableName), $copy->getTable($tableName)));
            $sql = array_merge($sql,
                $this->upgradeIndexes($master->getTable($tableName), $copy->getTable($tableName)));
        }

        return $sql;
    }


    /**
     * Compare the two sets of columns in $master and $copy
     *
     * @param TableInterface $master
     * @param TableInterface $copy
     * @return string[] of sql alter table statements
     */
    public function compareTableColumns(TableInterface $master, TableInterface $copy)
    {
        $masterColumnCount = $master->getColumnCount();
        $copyColumnCount   = $copy->getColumnCount();

        $sql = array();
        for ($masterColumnNumber = $copyColumnNumber = 0; $masterColumnNumber < $masterColumnCount; $masterColumnNumber++) {
            // search for the master column in the copy
            // Only need to search forward as all fields match up to the current position in each table.
            $found = false;
            for ($c = $copyColumnNumber; $c < $copyColumnCount && !$found; $c++) {
                $comparison = $master->getColumnNumber($masterColumnNumber)->compareTo($copy->getColumnNumber($c));
                if ($comparison == Column::NOT_EQUAL) {
                    continue; // keep searching for a column that matches (fully or partially)
                }
                if ($comparison == Column::PARTIAL_MATCH) {
                    $sql[] = $this->modifyColumn($copy,
                        $master->getColumnNumber($masterColumnNumber),
                        $copy->getColumnNumber($c));
                }

                if ($copyColumnNumber < $c) {
                    $sql = array_merge($sql, $this->deleteColumnsBetween($copyColumnNumber, $c, $copy));
                }
                $copyColumnNumber = $c + 1;
                $found            = true;
            }
            if (!$found) {
                $sql[] = $this->addColumn($master, $masterColumnNumber);
            }
        }
        // delete all the extra columns to the end
        $sql = array_merge($sql, $this->deleteColumnsBetween($copyColumnNumber, $copyColumnCount, $copy));

        return $sql;
    }


    /**
     * add a new column to the table
     * @param TableInterface $master             provides new column names and column types
     * @param int   $masterColumnNumber number of new column to add
     * @return string sql ddl
     */
    public function addColumn(TableInterface $master, $masterColumnNumber)
    {
        $location = $masterColumnNumber <= 0 ? 'first' : ('after ' .
            Schema::quoteIdentifier($master->getColumnNumber($masterColumnNumber - 1)->getName()));

        return 'alter table ' . Schema::quoteIdentifier($master->getName())
        . ' add column '
        . $master->getColumnNumber($masterColumnNumber)->toSQL()
        . ' ' . $location;
    }


    /**
     * return SQL to change $copy into $master
     * @param TableInterface  $table
     * @param Column $master
     * @param Column $copy
     * @return string sql ddl
     */
    public function modifyColumn(TableInterface $table, Column $master, Column $copy)
    {
        return 'alter table ' . Schema::quoteIdentifier($table->getName()) . ' change ' . Schema::quoteIdentifier($copy->getName()) . ' ' . $master->toSQL();
    }


    /**
     * return alter table sql to delete the selected columns
     * @param int   $start  starting column number to delete
     * @param int   $finish delete columns up to (but not including) this column number
     * @param TableInterface $copy   to delete columns from
     * @return string sql ddl
     */
    public function deleteColumnsBetween($start, $finish, TableInterface $copy)
    {
        $sql = array();
        for ($i = $start; $i < $finish; $i++) {
            $sql[] = 'alter table ' . Schema::quoteIdentifier($copy->getName())
                . ' drop column ' . Schema::quoteIdentifier($copy->getColumnNumber($i)->getName());
        }

        return $sql;
    }


    /**
     * get the names of all the tables in $db
     * @param SchemaInterface $db
     * @return string[] table name
     */
    public function getTableNames(SchemaInterface $db)
    {
        return array_diff($db->getTableNames(), $this->ignoredTables);
    }


    /**
     * see which indexes need to be added / deleted.
     * @param TableInterface $master
     * @param TableInterface $copy
     * @return string sql ddl
     */
    public function upgradeIndexes(TableInterface $master, TableInterface $copy)
    {
        $masterIndexes = $master->getIndexNames();
        $copyIndexes   = $copy->getIndexNames();
        $sql           = array();

        // all indexes not in $copy will be created
        foreach (array_diff($masterIndexes, $copyIndexes) as $name) {
            $sql[] = 'alter table ' . Schema::quoteIdentifier($copy->getName())
                . ' add ' . $master->getIndexByName($name)->toSQL();
        }

        // all indexes not in $master will be dropped
        foreach (array_diff($copyIndexes, $masterIndexes) as $name) {
            $sql[] = 'alter table ' . Schema::quoteIdentifier($copy->getName())
                . ' drop index ' . Schema::quoteIdentifier($name);
        }

        // indexes in both will be compared: if any change, they will be dropped and re-created
        foreach (array_intersect($masterIndexes, $copyIndexes) as $name) {
            if ($master->getIndexByName($name)->toSQL() == $copy->getIndexByName($name)->toSQL()) {
                continue; // match OK: do nothing
            }

            $sql[] = 'alter table ' . Schema::quoteIdentifier($copy->getName())
                . ' drop index ' . Schema::quoteIdentifier($name);
            $sql[] = 'alter table ' . Schema::quoteIdentifier($copy->getName())
                . ' add ' . $master->getIndexByName($name)->toSQL();
        }

        return $sql;
    }
}
