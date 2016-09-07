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
class DateTimeColumn extends Column
{
    public function __construct($name, $description = '', $allowNull = false)
    {
        parent::__construct($name, $description, $allowNull);
    }

    public function getSQLType()
    {
        return 'datetime';
    }

    public function getDefaultValue()
    {
        return '0000-00-00 00:00:00';
    }

    public function getSQLDefault()
    {
        return 'default \'' . $this->getDefaultValue() . '\'';
    }
}
