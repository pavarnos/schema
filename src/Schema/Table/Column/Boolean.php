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


    public function __construct($name, $description = '', $allowNull = false, $digits = self::BOOLEAN_DIGITS)
    {
        $allowNull = false;
        parent::__construct($name, $description, $allowNull, self::BOOLEAN_SIZE, $digits);
    }
}
