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
class TextColumn extends Column
{
    /** @var string  */
    private $size = ''; // tiny, medium, '' etc


    public function __construct($name, $description = '', $allowNull = false, $size = '')
    {
        $this->size = $size;
        parent::__construct($name, $description, $allowNull);
    }


    public function getSQLType()
    {
        return $this->size . 'text';
    }


    public function getSQLDefault()
    {
        if ($this->isAllowedNull()) {
            return 'default null';
        }
        return 'default \'\'';
    }


    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }
}
