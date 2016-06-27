<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema;

use LSS\Schema;
use LSS\Schema\Table\Column;
use LSS\Schema\Table\Index;

/**
 * Class Table
 * @package LSS\Schema
 */
class Table implements \IteratorAggregate
{
    use Tagable;

    /** @var string name of table (must be a valid SQL identifier) */
    private $name;

    /** @var string */
    private $description;

    /** @var Column[] */
    private $column = [ ];

    /** @var string[] */
    private $columnMap = [ ];

    /** @var Index[] */
    private $index = array();


    /**
     * @param $name        string name of the table (must be a valid SQL identifier)
     * @param $description string textual description of the table, which may
     *                     be included as a table comment in SQL or displayed to the user
     *                     in an admin interface.
     */
    public function __construct($name, $description = '')
    {
        $this->name        = $name;
        $this->description = $description;
    }


    /**
     * get the name of this table
     * @return string table name
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * get the textual description of this table
     * @return string table description
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * implements array iteration over tables
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->column);
    }


    /**
     * implements array iteration over tables
     * @return \ArrayIterator
     */
    public function getIndexIterator()
    {
        return new \ArrayIterator($this->index);
    }


    /**
     * @param Column $column
     * @return $this
     */
    public function addColumn(Column $column)
    {
        $this->column[$column->getName()] = $column;
        unset($this->columnMap);

        return $this;
    }


    /**
     * @param string $name
     * @return Column
     */
    public function getColumnByName($name)
    {
        if (!isset($this->column[$name])) {
            throw new \InvalidArgumentException('No such column ' . $name);
        }

        return $this->column[$name];
    }


    /**
     * @param int $index
     * @return Column
     */
    public function getColumnNumber($index)
    {
        if (empty($this->columnMap)) {
            $this->columnMap = array_keys($this->column);
        }
        if (!isset($this->columnMap[$index])) {
            throw new \InvalidArgumentException('No such column index ' . $index . ' in table ' . $this->getName());
        }

        return $this->column[$this->columnMap[$index]];
    }


    /**
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->column);
    }


    /**
     * @param Index $index
     * @return $this
     */
    public function addIndex(Index $index)
    {
        $this->index[$index->getName()] = $index;

        return $this;
    }


    /**
     * @param string $name
     * @return Column
     */
    public function getIndexByName($name)
    {
        if (!isset($this->index[$name])) {
            throw new \InvalidArgumentException('No such index ' . $name);
        }

        return $this->index[$name];
    }


    /**
     * @return int
     */
    public function getIndexCount()
    {
        return count($this->index);
    }


    /**
     * @return string[]
     */
    public function getIndexNames()
    {
        return array_keys($this->index);
    }


    /**
     * render the table as DDL
     * @param string $newline
     * @param string $columnSeparator
     * @return string generated sql
     */
    public function toSQL($newline = "\n", $columnSeparator = ",\n")
    {
        if ($this->getColumnCount() <= 0) {
            return '';
        }

        $items = array();
        foreach ($this->column as $column) {
            $items[] = $column->toSQL();
        }
        foreach ($this->index as $index) {
            $items[] = $index->toSQL();
        }

        $output = 'create table ' . Schema::quoteIdentifier($this->getName()) . ' (' . $newline;
        $output .= join($columnSeparator, $items) . $newline . ')';
        if (strlen($this->getDescription()) > 0) {
            $output .= ' comment=' . Schema::quoteDescription($this->getDescription());
        }
        // add Type, encoding etc here
        // deliberately no ; sql statement separator here: it is added later if needed

        return $output;
    }
}
