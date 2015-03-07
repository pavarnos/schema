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
class Integer extends Column
{
    const DEFAULT_DIGITS = 9;

    /** @var string  */
    private $size = ''; // tiny, medium, '' etc

    /** @var int  */
    private $digits = self::DEFAULT_DIGITS;


    public function __construct($name, $description = '', $allowNull = false, $size = '', $digits = self::DEFAULT_DIGITS)
    {
        $this->size = $size;
        $this->digits = $digits;
        parent::__construct($name, $description, $allowNull);
    }


    public function getSQLType()
    {
        return $this->size . 'int(' . $this->digits . ')';
    }


    public function getSQLDefault()
    {
        return 'default \'0\'';
    }


    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getDigits()
    {
        return $this->digits;
    }
}
