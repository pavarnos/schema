<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema\Table\Column;


/**
 */
class FloatColumn extends Column
{
    const DEFAULT_WIDTH = 9;
    const DEFAULT_DECIMAL_PLACES = 2;

    /** @var int precision */
    private $width = self::DEFAULT_WIDTH;

    /** @var int  */
    private $decimalPlaces = self::DEFAULT_DECIMAL_PLACES;


    public function __construct($name, $description = '', $allowNull = false, $width = self::DEFAULT_WIDTH, $decimalPlaces = self::DEFAULT_DECIMAL_PLACES)
    {
        $this->width = $width;
        $this->decimalPlaces = $decimalPlaces;
        parent::__construct($name, $description, $allowNull);
    }


    public function getSQLType()
    {
        return 'decimal(' . $this->width . ',' . $this->decimalPlaces . ')';
    }

    public function getDefaultValue()
    {
        return 0;
    }

    public function getSQLDefault()
    {
        return 'default \'' . $this->getDefaultValue() . '\'';
    }

    /**
     * the precision
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getDecimalPlaces()
    {
        return $this->decimalPlaces;
    }
}
