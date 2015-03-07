<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
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
class Schema implements \IteratorAggregate
{
    const BACKTICK = '`';
    const QUOTE = "'";

    /** @var Table[] */
    private $table = [ ];


    /**
     * Add a new table to the database
     * @param Table $table
     * @return Table
     */
    public function add(Table $table)
    {
        return $this->table[$table->getName()] = $table;
    }


    /**
     * return the number of tables added by add()
     * @return integer
     */
    public function getTableCount()
    {
        return count($this->table);
    }


    /**
     * @brief return the table
     * @param string $name of the table to return
     * @return Table table in the database
     */
    public function getTable($name)
    {
        if (!isset($this->table[$name])) {
            throw new \InvalidArgumentException('Unknown table ' . $name);
        }

        return $this->table[$name];
    }


    /**
     * names of all the tables
     * @return string[]
     */
    public function getTableNames()
    {
        return array_keys($this->table);
    }


    /**
     * implements array iteration over tables
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->table);
    }


    /**
     * return the $id quoted with BACKTICK
     * @param string $id the id to quote
     * @return string column name (quoted)
     */
    public static function quoteIdentifier($id)
    {
        if (empty($id)) {
            return '';
        }

        return self::BACKTICK . $id . self::BACKTICK;
    }


    /**
     * @param string $text
     * @return string quoted $text
     */
    static function quoteDescription($text)
    {
        return self::QUOTE . str_replace(self::QUOTE, self::QUOTE . self::QUOTE, $text) . self::QUOTE;
    }


    /**
     * @param string $text
     * @return string quoted $text
     */
    static function quoteEnumValue($text)
    {
        return self::quoteDescription($text);
    }


    /**
     * @param string $value
     * @return string
     */
    static public function unQuote($value)
    {
        if (substr($value, 0, 1) == substr($value, -1) && substr($value, 0, 1) == '"') {
            $value = substr($value, 1, -1);
        }
        if (substr($value, 0, 1) == substr($value, -1) && substr($value, 0, 1) == self::BACKTICK) {
            $value = substr($value, 1, -1);
        }
        if (substr($value, 0, 1) == substr($value, -1) && substr($value, 0, 1) == "'") {
            $value = substr($value, 1, -1);
        }
        $value = str_replace("''", "'", $value);

        return trim($value);
    }
}
