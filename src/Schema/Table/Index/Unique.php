<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table\Index;

use LSS\Schema\Table\Index;
use LSS\Schema;

/**
 * Class Column
 */
class Unique extends Index
{
    public function __construct($name, $columns = [ ], $type = 'UNIQUE')
    {
        parent::__construct($name, $columns, $type);
    }
}
