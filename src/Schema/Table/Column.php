<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table;

use LSS\Schema;

/**
 * Class Column
 */
abstract class Column
{
    /** @var string */
    private $name;

    /** @var string */
    private $description = '';

    /** @var bool */
    private $allowNull = false;

    const NOT_EQUAL = 0; // returned from compareTo()
    const EQUAL = 1; // returned from compareTo()
    const PARTIAL_MATCH = 2; // returned from compareTo()


    /**
     * @param string $name sql column name
     * @param string $description
     * @param bool   $allowNull
     */
    public function __construct($name, $description = '', $allowNull = false)
    {
        $this->name        = $name;
        $this->description = $description;
        $this->allowNull   = $allowNull;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @return string
     */
    abstract public function getSQLType();


    /**
     * @return string
     */
    public function getNullSQL()
    {
        return ($this->allowNull ? '' : ' not null');
    }


    /**
     * @return string
     */
    public function getSQLDefault()
    {
        return '';
    }


    public function toSQL()
    {
        $sql = Schema::quoteIdentifier($this->getName()) . ' ' . $this->getSQLType();
        if (!$this->allowNull) {
            $sql .= ' not null';
        }
        $default = $this->getSQLDefault();
        if (!empty($default)) {
            $sql .= ' ' . $default;
        }
        if ($this->description != '')
        {
            $sql .= ' comment ' . Schema::quoteDescription( $this->getDescription() );
        }
        return $sql;
    }


    /**
     * @param Column $other
     * @return int Column::EQUAL if the columns are an exact match,
     * | Column::NOT_EQUAL if nothing matches
     * | Column::PARTIAL_MATCH if enough matches that we can assume it was updated
     */
    public function compareTo($other)
    {
        if ($this->description == '' && $other->description == '') {
            // if no description, can change type only
            if ($this->name != $other->name) {
                return self::NOT_EQUAL;
            }

            return $this->getSQLType() == $other->getSQLType() ? self::EQUAL : self::PARTIAL_MATCH;
        }

        if ($this->name == $other->name) {
            if ($this->getSQLType() != $other->getSQLType()) {
                return $this->description == $other->description ? self::PARTIAL_MATCH : self::NOT_EQUAL;
            }

            return $this->description == $other->description ? self::EQUAL : self::PARTIAL_MATCH;
        } else {
            if ($this->getSQLType() != $other->getSQLType()) {
                // its tempting to allow a difference in description to generate a partial match,
                // but if you do this a sequence of columns of the same type will all get altered
                // and it becomes impossible to add a new column at the start of the table
                return self::NOT_EQUAL;
//                return $this->description == $other->description ? self::PARTIAL_MATCH : self::NOT_EQUAL;
            }

            return $this->description == $other->description ? self::PARTIAL_MATCH : self::NOT_EQUAL;
        }
    }
}
