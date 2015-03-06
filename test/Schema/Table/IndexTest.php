<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table;

use LSS\Schema;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $subject = new Index($name = 'abc', $columns = [ $one = 'a', $two = 'cc' ], $type = 'TheType');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($type . ' KEY ' . Schema::quoteIdentifier($name)
            . ' (' . Schema::quoteEnumValue($one) . ',' . Schema::quoteEnumValue($two) . ')', $subject->toSQL());
    }
}
