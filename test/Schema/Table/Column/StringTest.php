<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $subject = new String($name = 'abc', $desc = 'def', false, $length = 19);
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' varchar(' . $length . ') not null default \'\' comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
