<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table\Column;


/**
 */
class Boolean extends Integer
{
    const BOOLEAN_SIZE = 'tiny';
    const BOOLEAN_DIGITS = 4;


    public function __construct($name, $description = '', $allowNull = false, $size = self::BOOLEAN_SIZE, $digits = self::BOOLEAN_DIGITS)
    {
        $allowNull = false;
        parent::__construct($name, $description, $allowNull, $size, $digits);
    }
}
