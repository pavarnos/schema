<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    11 2016
 */
namespace LSS\Schema;

use LSS\Schema\Table\Column;
use LSS\Schema\Table\Index;

/**
 * Class Table
 * @package LSS\Schema
 */
interface TableInterface
{
    /**
     * get the name of this table
     * @return string table name
     */
    public function getName();

    /**
     * get the textual description of this table
     * @return string table description
     */
    public function getDescription();

    /**
     * implements array iteration over columns
     * @return \ArrayIterator
     */
    public function getIterator();

    /**
     * implements array iteration over tables
     * @return \ArrayIterator
     */
    public function getIndexIterator();

    /**
     * @param Column $column
     * @return $this
     */
    public function addColumn(Column $column);

    /**
     * @param string $name
     * @return Column
     */
    public function getColumnByName($name);

    /**
     * @param int $index
     * @return Column
     */
    public function getColumnNumber($index);

    /**
     * @return int
     */
    public function getColumnCount();

    /**
     * @param string $columnName
     * @return boolean
     */
    public function hasColumn($columnName);

    /**
     * implements array iteration over tables
     * @return Column[] keyed by name
     */
    public function getColumns();

    /**
     * @param Index $index
     * @return $this
     */
    public function addIndex(Index $index);

    /**
     * @param string $name
     * @return Index
     */
    public function getIndexByName($name);

    /**
     * @return int
     */
    public function getIndexCount();

    /**
     * @return string[]
     */
    public function getIndexNames();

    /**
     * @return Index[] keyed by name
     */
    public function getIndexes();

    /**
     * render the table as DDL
     * @param string $newline
     * @param string $columnSeparator
     * @return string generated sql
     */
    public function toSQL($newline = "\n", $columnSeparator = ",\n");
}
