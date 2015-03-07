<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class ForeignKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->setExpectedException('InvalidArgumentException');
        $subject = new ForeignKey($name = 'abc', $desc = 'def');
    }


    public function testConstructorRelatedInDescription()
    {
        $subject = new ForeignKey($name = 'abc', $desc = ForeignKey::RELATED_TEXT . ' other_table');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals('other_table', $subject->getOtherTable());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' ' . $subject::DEFAULT_SIZE . 'int(' . $subject::DEFAULT_DIGITS . ') not null  comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }


    public function testConstructorRelatedAsParameter()
    {
        $subject = new ForeignKey($name = 'abc', '', false, $size = 'long', $digits = 12, $other = 'my_other_table');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc = $subject::RELATED_TEXT . ' ' . $other, $subject->getDescription());
        $this->assertEquals($other, $subject->getOtherTable());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' ' . $size . 'int(' . $digits . ') not null  comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
