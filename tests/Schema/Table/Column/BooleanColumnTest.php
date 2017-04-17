<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class BooleanColumnTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $subject = new BooleanColumn($name = 'abc', $desc = 'def');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' tinyint(4) not null default \'0\' comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
