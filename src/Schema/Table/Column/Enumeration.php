<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema\Table\Column;
use LSS\Schema;


/**
 */
class Enumeration extends Column
{
    /** @var array string */
    private $values = [ ];

    protected $type = 'enum';


    public function __construct($name, $description = '', $allowNull = false, $values = '')
    {
        $this->parseValues($values);
        parent::__construct($name, $description, $allowNull);
    }


    /**
     * @param string[] | string $values
     */
    public function parseValues($values)
    {
        if (!is_array($values)) {
            $values = array_map('trim',explode(',', $values));
            $values = array_map('LSS\Schema::unQuote', $values);
        }
        foreach ($values as $value) {
            $this->values[$value] = $value;
        }
    }


    public function getSQLType()
    {
        return $this->type . ' (' . join(',', array_map('LSS\Schema::quoteEnumValue', $this->values) ) . ')';
    }


    public function getSQLDefault()
    {
        $default = array_values($this->values);
        $default = $default[0];

        return 'default ' . Schema::quoteEnumValue($default);
    }
}
