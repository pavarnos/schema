<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table\Column;

use LSS\Schema;

class PrimaryKeyColumnTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $subject = new PrimaryKeyColumn($name = 'abc', $desc = 'def');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(Schema::quoteIdentifier($name) . ' ' . $subject::DEFAULT_SIZE . 'int(' . $subject::DEFAULT_DIGITS . ') auto_increment not null comment ' . Schema::QUOTE . $desc . Schema::QUOTE, $subject->toSQL());
    }
}
