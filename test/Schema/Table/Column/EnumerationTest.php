<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class EnumerationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorArray()
    {
        $subject = new Enumeration($name = 'abc', $desc = 'def', false, [$one = 'one', $two = 'two', $three = 'three']);
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name)
            . ' enum ('. Schema::quoteEnumValue($one) . ',' . Schema::quoteEnumValue($two) . ',' . Schema::quoteEnumValue($three)
            .') not null default ' . Schema::quoteEnumValue($one) . ' comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }


    public function testConstructorString()
    {
        $subject = new Enumeration($name = 'abc', $desc = 'def', false, '"' . ($one = 'one') . '","' . ($two = 'two') . '","' . ($three = 'three') . '"');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name)
            . ' enum ('. Schema::quoteEnumValue($one) . ',' . Schema::quoteEnumValue($two) . ',' . Schema::quoteEnumValue($three)
            .') not null default ' . Schema::quoteEnumValue($one) . ' comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
