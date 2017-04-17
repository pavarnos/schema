<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table;

use LSS\Schema;

class IndexTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $subject = new Index($name = 'abc', $columns = [ $one = 'a', $two = 'cc' ], $type = 'TheType');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($type . ' key ' . Schema::quoteIdentifier($name)
            . ' (' . Schema::quoteIdentifier($one) . ',' . Schema::quoteIdentifier($two) . ')', $subject->toSQL());
    }


    public function testConstructor2()
    {
        $subject = new Index($name = 'abc');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals('key ' . Schema::quoteIdentifier($name)
            . ' (' . Schema::quoteIdentifier($name) . ')', $subject->toSQL());
    }
}
