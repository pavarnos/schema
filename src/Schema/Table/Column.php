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
        $sql = Schema::quoteIdentifier($this->getName()) . ' ' . $this->getSQLType() . ($this->allowNull ? '' : ' not null ') . $this->getSQLDefault();
        if ($this->description != '')
        {
            $sql .= ' comment ' . Schema::quoteDescription( $this->getDescription() );
        }
        return $sql;
    }


    /**
     * @param Column $b
     * @return int Column::EQUAL if the columns are an exact match,
     * | Column::NOT_EQUAL if nothing matches
     * | Column::PARTIAL_MATCH if enough matches that we can assume it was updated
     */
    public function compareTo($b)
    {
        if ($this->description == '' && $b->description == '') {
            // if no description, can change type only
            if ($this->name != $b->name) {
                return self::NOT_EQUAL;
            }

            return $this->getSQLType() == $b->getSQLType() ? self::EQUAL : self::PARTIAL_MATCH;
        }

        // can change any one of (name, type, description)
        if ($this->name == $b->name) {
            if ($this->getSQLType() != $b->getSQLType()) {
                return $this->description == $b->description ? self::PARTIAL_MATCH : self::NOT_EQUAL;
            }

            return $this->description == $b->description ? self::EQUAL : self::PARTIAL_MATCH;
        } else {
            if ($this->getSQLType() != $b->getSQLType()) {
                return self::NOT_EQUAL;
            }

            return $this->description == $b->description ? self::PARTIAL_MATCH : self::NOT_EQUAL;
        }
    }
}
