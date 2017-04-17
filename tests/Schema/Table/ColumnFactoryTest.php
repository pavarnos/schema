<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Table;

use LSS\Schema;
use LSS\Schema\Table\Column\EnumerationColumn;

class ColumnFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testEnum()
    {
        $text = "enum('Boolean','Integer','Decimal','Select','Currency','FreeText','HTML') NOT NULL DEFAULT 'Boolean'";
        $subject = new ColumnFactory();
        /** @var EnumerationColumn $column */
        $column = $subject->create('aaa', 'bbb', $text);
        $this->assertInstanceOf('LSS\Schema\Table\Column\EnumerationColumn', $column);
        $values = ['Boolean','Integer','Decimal','Select','Currency','FreeText','HTML'];
        $values = array_combine($values, $values);
        $this->assertEquals($values, $column->getValues());
    }
}
