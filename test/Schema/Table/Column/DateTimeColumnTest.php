<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class DateTimeColumnTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $subject = new DateTimeColumn($name = 'abc', $desc = 'def');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' datetime not null default \'0000-00-00 00:00:00\' comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
