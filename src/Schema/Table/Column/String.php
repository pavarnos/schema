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
class String extends Column
{
    const DEFAULT_LENGTH = 50;

    /** @var integer  */
    private $length = self::DEFAULT_LENGTH;


    public function __construct($name, $description = '', $allowNull = false, $length = self::DEFAULT_LENGTH)
    {
        $this->length = $length;
        parent::__construct($name, $description, $allowNull);
    }


    public function getSQLType()
    {
        return 'varchar(' . $this->length . ')';
    }


    public function getSQLDefault()
    {
        return 'default \'\'';
    }


    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }
}
