<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class TextColumnTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $subject = new TextColumn($name = 'abc', $desc = 'def', false, 'medium');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertFalse($subject->isAllowedNull());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' mediumtext not null default \'\' comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }

    public function testConstructorWithNull()
    {
        $subject = new TextColumn($name = 'abc', $desc = 'def', true, 'medium');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertTrue($subject->isAllowedNull());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' mediumtext null default null comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
