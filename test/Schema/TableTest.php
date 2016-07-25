<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema;

use LSS\Schema\Table\Column\StringColumn;
use LSS\Schema\Table\Index;

class TableTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $subject = new Table($name = 'abc', $desc = 'def');
        $this->assertEquals($name, $subject->getName());
        $this->assertEquals($desc, $subject->getDescription());
        $this->assertEquals(0, $subject->getColumnCount());
        $this->assertEquals(0, $subject->getIndexCount());
    }


    public function testAddColumn()
    {
        $subject = new Table($name = 'abc', $desc = 'def');
        $column = new StringColumn($colName = 'ghi', $colDesc = 'jkl');
        $result = $subject->addColumn($column);
        $this->assertEquals($result, $subject, 'to allow fluent adding of many columns');
        $this->assertEquals($column, $subject->getColumnByName($colName));
        $this->assertEquals($column, $subject->getColumnNumber(0));
        $this->assertEquals(1, $subject->getColumnCount());
        $this->assertTrue($subject->hasColumn($colName));
        $this->assertFalse($subject->hasColumn('non_existent_column'));

        $column2 = new StringColumn($colName2 = 'ghi2', $colDesc2 = 'jkl2');
        $subject->addColumn($column2);
        $this->assertEquals($column2, $subject->getColumnByName($colName2));
        $this->assertEquals($column2, $subject->getColumnNumber(1));
        $this->assertEquals(2, $subject->getColumnCount());

        // re-adding the same column should not change the count
        $subject->addColumn($column2);
        $this->assertEquals(2, $subject->getColumnCount());
    }


    public function testAddIndex()
    {
        $subject = new Table($name = 'abc', $desc = 'def');
        $index = new Index($indexName = 'ghi');
        $result = $subject->addIndex($index);
        $this->assertEquals($result, $subject, 'to allow fluent adding of many indexes');
        $this->assertEquals($index, $subject->getIndexByName($indexName));
        $this->assertEquals(1, $subject->getIndexCount());
    }
}
