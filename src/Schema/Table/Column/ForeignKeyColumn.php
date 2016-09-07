<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table\Column;

/**
 *
 */
class ForeignKeyColumn extends IntegerColumn
{
    const DEFAULT_SIZE = 'medium';
    const DEFAULT_DIGITS = 9;
    const RELATED_TEXT = 'The related';

    /** @var string name of foreign table */
    private $otherTable;

    public function __construct(
        $name,
        $description = '',
        $allowNull = false,
        $size = self::DEFAULT_SIZE,
        $digits = self::DEFAULT_DIGITS,
        $otherTable = ''
    ) {
        if (empty($otherTable)) {
            if (preg_match('|^' . self::RELATED_TEXT . '\s+([-A-Za-z_]+)|', $description, $matches)) {
                $otherTable = $matches[1];
            } else {
                throw new \InvalidArgumentException('Cannot determine the related table');
            }
        }
        if (empty($description)) {
            $description = self::RELATED_TEXT . ' ' . $otherTable;
        }
        $this->otherTable = $otherTable;
        parent::__construct($name, $description, $allowNull, $size, $digits);
    }

    public function getDefaultValue()
    {
        return 0;
    }


//    public function getSQLType()
//    {
//        return parent::getSQLType();
//    }
//

//    public function getSQLDefault()
//    {
//        return '';
//    }
//

    /**
     * @return string
     */
    public function getOtherTable()
    {
        return $this->otherTable;
    }
}
