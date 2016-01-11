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
class DateColumn extends Column
{
    public function __construct($name, $description = '', $allowNull = false)
    {
        parent::__construct($name, $description, $allowNull);
    }


    public function getSQLType()
    {
        return 'date';
    }


    public function getSQLDefault()
    {
        return 'default \'0000-00-00\'';
    }
}
