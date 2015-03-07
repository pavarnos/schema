<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema\Renderer;

use LSS\Schema;
use LSS\Schema\Table;
use LSS\Schema\Table\Column;
use LSS\Schema\Table\Index;

class AlterTableSQLTest extends \PHPUnit_Framework_TestCase
{
    public function testAddColumn()
    {
        $table = new Table('one');
        $table->addColumn($col1 =new Column\Integer($name = 'c1', $desc = 'my desc', false, $size = 'medium', $digits = 5));
        $table->addColumn($col2 =new Column\Integer($name2 = 'c2'));

        $subject = new AlterTableSQL();
        $sql     = $subject->addColumn($table, 0); // add it as the first column
        $this->assertEquals('alter table ' . Schema::quoteIdentifier('one')
            . ' add column first ' . $col1->toSQL(), $sql );

        $sql     = $subject->addColumn($table, 1); // a second or later column
        $this->assertEquals('alter table ' . Schema::quoteIdentifier('one')
            . ' add column after ' . Schema::quoteIdentifier($name) . ' ' . $col2->toSQL(), $sql );
    }


    public function testModifyColumn()
    {
        $table = new Table('one');
        $table->addColumn($col1 =new Column\Integer($name = 'c1', $desc = 'my desc', false, $size = 'medium', $digits = 5));
        $col2 =new Column\Integer($name);

        $subject = new AlterTableSQL();
        $sql     = $subject->modifyColumn($table, $col1, $col2);
        $this->assertEquals('alter table ' . Schema::quoteIdentifier('one')
            . ' change ' . Schema::quoteIdentifier($name) . ' ' . $col1->toSQL(), $sql );
    }


    public function testDeleteColumnsBetween()
    {
        $table = new Table('one');
        $table->addColumn(new Column\Integer($col1 = 'c1'));
        $table->addColumn(new Column\Integer($col2 = 'c2'));
        $table->addColumn(new Column\Integer($col3 = 'c3'));
        $table->addColumn(new Column\Integer($col4 = 'c4'));

        $subject = new AlterTableSQL();
        $sql     = $subject->deleteColumnsBetween(1, 3, $table); // the middle two columns
        $this->assertEquals(2, count($sql), 'last index is not inclusive');
        $this->assertEquals($sql[0], 'alter table ' . Schema::quoteIdentifier('one') . ' drop column ' . Schema::quoteIdentifier($col2) );
        $this->assertEquals($sql[1], 'alter table ' . Schema::quoteIdentifier('one') . ' drop column ' . Schema::quoteIdentifier($col3) );
    }


    public function testUpgradeIndexes()
    {
        $one = new Table('one');
        $one->addIndex(new Index\Primary($index1 = 'indexone'));
        $one->addIndex(new Index($index2 = 'indextwo', [ $two1 = 'a', $two2 = 'bbb' ]));
        $two = new Table('two');
        $two->addIndex(new Index($index3 = 'indexthree'));
        $two->addIndex(new Index($index1));

        $subject = new AlterTableSQL();
        $sql     = $subject->upgradeIndexes($one, $two);
        $this->assertEquals(4, count($sql));
        $this->assertEquals('alter table ' . Schema::quoteIdentifier('two')
            . ' add key ' . Schema::quoteIdentifier($index2)
            . ' (' . Schema::quoteIdentifier($two1) . ',' . Schema::quoteIdentifier($two2) . ')', $sql[0]);
        $this->assertEquals('alter table ' . Schema::quoteIdentifier('two')
            . ' drop index ' . Schema::quoteIdentifier($index3), $sql[1]);
        $this->assertEquals('alter table ' . Schema::quoteIdentifier('two')
            . ' drop index ' . Schema::quoteIdentifier($index1), $sql[2]);
        $this->assertEquals('alter table ' . Schema::quoteIdentifier('two')
            . ' add primary key ' . Schema::quoteIdentifier($index1)
            . ' (' . Schema::quoteIdentifier($index1) . ')', $sql[3]);
    }


    public function testCompareTableColumnsNoChange()
    {
        // same columns in same order
        $master = new Table('the_table');
        $master->addColumn(new Column\Integer($col1 = 'c1', $desc1 = 'my desc'));
        $master->addColumn(new Column\Integer($col2 = 'c2', $desc2 = 'other desc'));
        $copy = new Table('the_table');
        $copy->addColumn(new Column\Integer($col1, $desc1));
        $copy->addColumn(new Column\Integer($col2, $desc2));
        $subject = new AlterTableSQL();
        $this->assertEmpty($subject->compareTableColumns($master, $copy));
    }


    public function testCompareTableColumnsAddOneAtStart()
    {
        $master = new Table('the_table');
        $master->addColumn(new Column\Integer($col1 = 'c1', $desc1 = 'my desc'));
        $master->addColumn(new Column\Integer($col2 = 'c2', $desc2 = 'other desc'));
        $master->addColumn(new Column\Integer($col3 = 'c3', $desc3 = 'other desc 3'));
        $copy = new Table('the_table');
        $copy->addColumn(new Column\Integer($col2, $desc2));
        $copy->addColumn(new Column\Integer($col3, $desc3));
        $subject = new AlterTableSQL();
        $sql = $subject->compareTableColumns($master, $copy);
        $this->assertEquals(1, count($sql));
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' add column first ', $sql[0]);
    }


    public function testCompareTableColumnsInsertOne()
    {
        $master = new Table('the_table');
        $master->addColumn(new Column\Integer($col1 = 'c1', $desc1 = 'my desc'));
        $master->addColumn(new Column\Integer($col2 = 'c2', $desc2 = 'other desc'));
        $master->addColumn(new Column\Integer($col3 = 'c3', $desc3 = 'other desc 3'));
        $copy = new Table('the_table');
        $copy->addColumn(new Column\Integer($col1, $desc1));
        $copy->addColumn(new Column\Integer($col3, $desc3));
        $subject = new AlterTableSQL();
        $sql = $subject->compareTableColumns($master, $copy);
        $this->assertEquals(1, count($sql));
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' add column after ' . Schema::quoteIdentifier($col1), $sql[0]);
    }


    public function testCompareTableColumnsDeleteTwo()
    {
        $copy = new Table('the_table');
        $copy->addColumn(new Column\Integer($col1 = 'c1', $desc1 = 'my desc'));
        $copy->addColumn(new Column\Integer($col2 = 'c2', $desc2 = 'other desc'));
        $copy->addColumn(new Column\Integer($col3 = 'c3', $desc3 = 'other desc 3'));
        $master = new Table('the_table');
        $master->addColumn(new Column\Integer($col3, $desc3));
        $subject = new AlterTableSQL();
        $sql = $subject->compareTableColumns($master, $copy);
        $this->assertEquals(2, count($sql));
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' drop column ' . Schema::quoteIdentifier($col1), $sql[0]);
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' drop column ' . Schema::quoteIdentifier($col2), $sql[1]);
    }


    public function testCompareTableColumnsAddOneAtEnd()
    {
        $master = new Table('the_table');
        $master->addColumn(new Column\Integer($col1 = 'c1', $desc1 = 'my desc'));
        $master->addColumn(new Column\Integer($col2 = 'c2', $desc2 = 'other desc'));
        $master->addColumn(new Column\Integer($col3 = 'c3', $desc3 = 'other desc 3'));
        $copy = new Table('the_table');
        $copy->addColumn(new Column\Integer($col1, $desc1));
        $copy->addColumn(new Column\Integer($col2, $desc2));
        $subject = new AlterTableSQL();
        $sql = $subject->compareTableColumns($master, $copy);
        $this->assertEquals(1, count($sql));
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' add column after ' . Schema::quoteIdentifier($col2), $sql[0]);
    }


    public function testCompareTableColumnsBigMessyChanges()
    {
        $copy = new Table('the_table');
        $copy->addColumn(new Column\PrimaryKey($col1 = 'id', $desc1 = 'the key'));
        $copy->addColumn(new Column\String($col2 = 'c2', $desc2 = 'column 2'));
        $copy->addColumn(new Column\Integer($col3 = 'c3', $desc3 = 'desc3'));
        $copy->addColumn(new Column\Integer($col4 = 'c4', $desc4 = 'desc4'));
        $copy->addColumn(new Column\Float($col5 = 'c5', $desc5 = 'desc5'));

        $master = new Table('the_table');
        $master->addColumn(new Column\PrimaryKey($col1, $desc1new = 'the new key')); // change desc
        // c2 deleted
        $master->addColumn(new Column\Integer($col3, $desc3)); // no change
        $master->addColumn(new Column\Integer($col3a = 'foobar', 'baz')); // inserted
        // c4 deleted
        $master->addColumn(new Column\String($col5, $desc5)); // change type
        $master->addColumn(new Column\String($col6 = 'c6')); // new column at end

        $subject = new AlterTableSQL();
        $sql = $subject->compareTableColumns($master, $copy);
        $this->assertEquals(6, count($sql));
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' change ' . Schema::quoteIdentifier($col1), $sql[0]);
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' drop column ' . Schema::quoteIdentifier($col2), $sql[1]);
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' add column after ' . Schema::quoteIdentifier($col3) . ' ' . Schema::quoteIdentifier($col3a), $sql[2]);
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' change ' . Schema::quoteIdentifier($col5), $sql[3]);
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' drop column ' . Schema::quoteIdentifier($col4), $sql[4]);
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('the_table') . ' add column after ' . Schema::quoteIdentifier($col5) . ' ' . Schema::quoteIdentifier($col6), $sql[5]);
    }


    /**
     * the final big bang test of all above functionality
     */
    public function testRender()
    {
        $masterOne = new Table('one');
        $masterOne->addColumn(new Column\PrimaryKey($col1_1 = 'id', $desc1_1 = 'the key'));
        $masterTwo = new Table('two');
        $masterTwo->addColumn(new Column\PrimaryKey($col2_1 = 'id', $desc2_1 = 'the key'));
        $masterTwo->addColumn(new Column\String($col2_2 = 'c2', $desc2_2 = 'column 2'));
        $masterTwo->addIndex(new Index($col2_2));
        $master = new Schema();
        $master->add($masterOne);
        $master->add($masterTwo);

        // table one is missing
        $copyTwo = new Table('two');
        $copyTwo->addColumn(new Column\PrimaryKey($col2_1, $desc2_1));
        $copyTwo->addColumn(new Column\String($col2_2 = 'c2', $desc2_2 = 'desc changed')); // description changed
        // index is missing
        // table three is extra and must be deleted
        $copyThree = new Table('three');
        $copyThree->addColumn(new Column\PrimaryKey($col3_1 = 'id', $desc3_1 = 'the key'));
        $copy = new Schema();
        $copy->add($copyTwo);
        $copy->add($copyThree);

        $subject = new AlterTableSQL();
        $sql = $subject->render($master, $copy);
        $this->assertEquals(4, count($sql));
        $this->assertStringStartsWith('create table ' . Schema::quoteIdentifier('one'), $sql[0]);
        $this->assertStringStartsWith('drop table ' . Schema::quoteIdentifier('three'), $sql[1]);
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('two') . ' change ' . Schema::quoteIdentifier($col2_2), $sql[2]);
        $this->assertStringStartsWith('alter table ' . Schema::quoteIdentifier('two') . ' add key ' . Schema::quoteIdentifier($col2_2), $sql[3]);
    }
}
