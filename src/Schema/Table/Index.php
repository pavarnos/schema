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
        if (empty($columns)) {
            // assume the index is named after the column it indexes
            $columns[]= $name;
        }
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
        return trim($this->type . ' key ' . Schema::quoteIdentifier($this->getName())
            . ' (' . join(',', array_map('LSS\Schema::quoteIdentifier', $this->columns)) . ')');
    }


    /**
     * @return string[]
     */
    public function getColumns()
    {
        return $this->columns;
    }
}
