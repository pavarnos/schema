<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table;

use LSS\Schema;

class ColumnTest extends \PHPUnit\Framework\TestCase
{
    const SQLTYPE = 'text';


    /**
     * @param string $name
     * @param string $desc
     * @param bool   $allowNull
     * @return Column
     */
    public function getSubject($name = 'abc', $desc = 'def', $allowNull = false)
    {
        $mock = $this->getMockBuilder('LSS\Schema\Table\Column')
            ->setConstructorArgs([$name, $desc, $allowNull])
            ->setMethods(['getSQLType'])
            ->getMockForAbstractClass();
        $mock->expects($this->any())->method('getSQLType')->willReturn(self::SQLTYPE);
        return $mock;
    }


    public function testConstructor()
    {
        $subject = $this->getSubject($name = 'abc', $desc = 'def', $allowNull = false);
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertNotEmpty($subject->getNullSQL());
        $this->assertEquals(Schema::BACKTICK . $name . Schema::BACKTICK . ' ' . self::SQLTYPE . ' not null comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }


    public function testConstructorWithSpaces()
    {
        $subject = $this->getSubject($name = ' abc  ', $desc = ' def   ', $allowNull = false);
        $this->assertEquals('abc', $subject->getName());
        $this->assertEquals('def', $subject->getDescription());
    }


    public function testCompareTo()
    {
        $a = new Schema\Table\Column\StringColumn($name = 'abc');
        $this->assertEquals($a::EQUAL, $a->compareTo(new Schema\Table\Column\StringColumn($name)));
        $this->assertEquals($a::NOT_EQUAL, $a->compareTo(new Schema\Table\Column\StringColumn($name . 'x')), 'name is different');
        $this->assertEquals($a::PARTIAL_MATCH, $a->compareTo(new Schema\Table\Column\StringColumn($name, 'def')), 'description is different');
        $this->assertEquals($a::PARTIAL_MATCH, $a->compareTo($this->getSubject($name, '')), 'because type is different');
        $this->assertEquals($a::NOT_EQUAL, $a->compareTo($this->getSubject($name, 'def')), 'name is different: cannot assume same just because they have the same type');
        $this->assertEquals($a::NOT_EQUAL, $a->compareTo($this->getSubject($name . 'x', '')), 'because more than one thing is different');
    }
}
