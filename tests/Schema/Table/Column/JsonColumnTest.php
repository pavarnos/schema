<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class JsonColumnTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $subject = new JsonColumn($name = 'abc', $desc = 'def');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' json not null comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
