<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class TextColumnTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $subject = new TextColumn($name = 'abc', $desc = 'def', false, 'medium');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' mediumtext not null default \'\' comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
