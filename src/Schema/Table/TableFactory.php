<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table;

use LSS\Schema\Table;
use LSS\Schema\TableInterface;

/**
 * Class TableFactory
 */
class TableFactory implements TableFactoryInterface
{
    /**
     * @param string $name name of the table
     * @param string $description
     * @return TableInterface
     */
    public function createTable($name, $description = '')
    {
        return new Table($name, $description);
    }
}
