<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table;

use LSS\Schema;

/**
 *
 */
class Index
{
    /** @var string */
    private $name;

    /** @var string[] */
    private $columns = [ ];

    /** @var string */
    private $type = '';


    /**
     * @param string   $name column name
     * @param string[] $columns
     * @param string   $type
     */
    public function __construct($name, $columns = [ ], $type = '')
    {
        $this->name    = $name;
        $this->columns = $columns;
        $this->type    = $type;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return string sql
     */
    public function toSQL()
    {
        return trim($this->type . ' KEY ' . Schema::quoteIdentifier($this->getName())
            . ' (' . join(',', array_map('LSS\Schema::quoteEnumValue', $this->columns)) . ')');
    }


    /**
     * @return \string[]
     */
    public function getColumns()
    {
        return $this->columns;
    }
}
