<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table\Renderer;

use LSS\Schema\Table;
use LSS\Schema\Table\Column;
use LSS\Schema;

/**
 * Class FullDDL
 * @package LSS\Schema\Table\Renderer
 *
 * Take the Schema and return a string containing the ALTER TABLE statements necessary to update a database
 */
class AlterTableSQL
{
    /**
     *
     * @param Schema   $master
     * @param Schema   $copy
     * @param string[] $ignoreTables names of tables you don't want to verify
     * @return string sql
     */
    public function render(Schema $master, Schema $copy, $ignoreTables = [ ])
    {
        $sql = array();

        $masterTables = $this->getTableNames($master, $ignoreTables);
        $copyTables   = $this->getTableNames($master, $ignoreTables);

        // all tables not in $copy will be created
        foreach (array_diff($masterTables, $copyTables) as $tableName) {
            $sql[] = $master->getTableByName($tableName)->toSQL();
        }

        // all tables not in $master will be dropped
        foreach (array_diff($copyTables, $masterTables) as $tableName) {
            $sql[] = 'drop table ' . $tableName;
        }

        // tables in both will be compared field by field, index by index
        foreach (array_intersect($masterTables, $copyTables) as $tableName) {
            $sql = array_merge($sql,
                $this->compareTableColumns($master->getTable($tableName), $copy->getTable($tableName)));
            $sql = array_merge($sql,
                $this->upgradeIndexes($master->getTable($tableName), $copy->getTable($tableName)));
            // todo primary index
        }

        return $sql;
    }


    //-------------------------------------------------------------------------
    /**
     * compare the two sets of columns in $master and $copy
     * Advances two indexes through the columns in each table.
     * Only need to search forward as all fields match up to the current
     * position in each table.
     * @param Table $master
     * @param Table $copy
     * @return array of sql alter table statements
     */
    public function compareTableColumns(Table $master, Table $copy)
    {
        $masterColumnCount = $master->getColumnCount();
        $copyColumnCount   = $copy->getColumnCount();
        $sql               = array();
        for ($masterColumnNumber = $copyColumnNumber = 0; $masterColumnNumber < $masterColumnCount; $masterColumnNumber++) {
            // search for the master column in the copy
            $found = false;
            for ($c = $copyColumnNumber; $c < $copyColumnCount && !$found; $c++) {
                $comparison = $master->getColumnNumber($masterColumnNumber)->compareTo($copy->getColumnNumber($c));
                if ($comparison == Column::NOT_EQUAL) {
                    continue;
                } // keep searching for a column that matches (fully or partially)
                if ($comparison == Column::PARTIAL_MATCH) {
                    $sql[] = $this->modifyColumn($copy, $master->getColumnNumber($masterColumnNumber),
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
     * @param Table $master             provides new column names and column types
     * @param int   $masterColumnNumber number of new column to add
     * @return string sql ddl
     */
    public function addColumn(Table $master, $masterColumnNumber)
    {
        return 'alter table ' . $master->getName() . ' add column ' . $master->getColumnNumber($masterColumnNumber)
            ->toSQL()
        . ($masterColumnNumber <= 0 ? ' first' : (' after ' . $master->getColumnNumber($masterColumnNumber - 1)
                ->getQuotedName()));
    }


    /**
     * return SQL to change $copy into $master
     * @param Table  $table
     * @param Column $master
     * @param Column $copy
     * @return string sql ddl
     */
    public function modifyColumn(Table $table, Column $master, Column $copy)
    {
        return 'alter table ' . $table->getName() . ' change ' . $copy->getQuotedName() . ' ' . $master->toSQL();
    }


    /**
     * return alter table sql to delete the selected columns
     * @param int   $start  starting column number to delete
     * @param int   $finish delete columns up to (but not including) this column number
     * @param Table $copy   to delete columns from
     * @return string sql ddl
     */
    public function deleteColumnsBetween($start, $finish, Table $copy)
    {
        $sql = array();
        for ($i = $start; $i < $finish; $i++) {
            $sql[] = 'alter table ' . $copy->getName() . ' drop column ' . $copy->getColumnNumber($i)->getQuotedName();
        }

        return $sql;
    }


    /**
     * get the names of all the tables in $db
     * @param Schema $db
     * @return string[] table name
     */
    public function getTableNames(Schema $db, $ignoredTables = [ ])
    {
        return array_diff($db->getTableNames(), $ignoredTables);
    }


    /**
     * see which indexes need to be added / deleted.
     * @param
     * @return string sql ddl
     */
    public function upgradeIndexes(Table $master, Table $copy)
    {
        $masterIndexes = $master->getIndexNames();
        $copyIndexes   = $copy->getIndexNames();
        $sql           = array();

        // all indexes not in $copy will be created
        foreach (array_diff($masterIndexes, $copyIndexes) as $name) {
            $sql[] = 'alter table ' . $copy->getName() . ' add ' . $master->getIndex($name)->toSQL();
        }

        // all indexes not in $master will be dropped
        foreach (array_diff($copyIndexes, $masterIndexes) as $name) {
            $sql[] = 'alter table ' . $copy->getName() . ' drop index `' . $name . '`';
        }

        // indexes in both will be compared: if any change, they will be dropped and re-created
        foreach (array_intersect($masterIndexes, $copyIndexes) as $name) {
            if ($master->getIndex($name)->compareTo($copy->getIndex($name))) {
                continue;
            } // match OK: do nothing

            $sql[] = 'alter table ' . $copy->getName() . ' drop index `' . $name . '`';
            $sql[] = 'alter table ' . $copy->getName() . ' add ' . $master->getIndex($name)->toSQL();
        }

        return $sql;
    }

}
