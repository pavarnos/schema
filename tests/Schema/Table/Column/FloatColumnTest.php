<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class FloatColumnTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $subject = new FloatColumn($name = 'abc', $desc = 'def', false, $width = 7, $dp = 3);
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' decimal(' . $width . ',' . $dp . ') not null default \'0\' comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
