<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    11 2016
 */
namespace LSS;

use LSS\Schema\Table;

/**
 * Models an SQL database in memory. Used to hold the parsed results of
 * a real DDL statement to generate a database, or from a PHP Schema definition file
 * which has code like this:
 *
 *   function GetDatabaseSchema( $database )
 *   {
 *       $database->addTable( 'card', 'Software requirements' )
 *           ->addPrimaryKeyColumn   ()
 *           ->addNestedSetColumns   ()
 *           ->addStringColumn       ( 'title'        , 100, 'Short title for the requirement' )
 *           ->addTextColumn         ( 'description'       , 'Longer description of how this could be implemented' )
 *           ->addTextColumn         ( 'tests'             , 'Acceptance tests and use-cases' )
 *           ->addLastModifiedColumn ();
 *   }
 *
 */
interface SchemaInterface
{
    /**
     * Add a new table to the database
     * @param Table $table
     * @return Table
     */
    public function add(Table $table);

    /**
     * return the number of tables added by add()
     * @return integer
     */
    public function getTableCount();

    /**
     * @brief return the table
     * @param string $name of the table to return
     * @return Table table in the database
     */
    public function getTable($name);

    /**
     * @brief return all the tables indexed by name
     * @return Table[] table in the database
     */
    public function getTables();

    /**
     * names of all the tables
     * @return string[] keyed by name
     */
    public function getTableNames();

    /**
     * implements array iteration over tables
     * @return \ArrayIterator
     */
    public function getIterator();
}
