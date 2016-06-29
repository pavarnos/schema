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
class JsonColumn extends Column
{
    public function __construct($name, $description = '', $allowNull = false)
    {
        parent::__construct($name, $description, $allowNull);
    }


    public function getSQLType()
    {
        return 'json';
    }
}
