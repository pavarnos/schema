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
class PrimaryKey extends Integer
{
    const DEFAULT_SIZE = 'medium';
    const DEFAULT_DIGITS = 9;

    public function __construct(
        $name,
        $description = '',
        $allowNull = false,
        $size = self::DEFAULT_SIZE,
        $digits = self::DEFAULT_DIGITS
    ) {
        $allowNull = false;
        parent::__construct($name, $description, $allowNull, $size, $digits);
    }


    public function getSQLType()
    {
        return parent::getSQLType() . ' auto_increment';
    }


    public function getSQLDefault()
    {
        return '';
    }
}
