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
 * Class Primary
 */
class Primary extends Index
{
    public function __construct($name, $columns = [ ], $type = 'primary')
    {
        parent::__construct($name, [$name], $type);
    }
}
