<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS;

use LSS\Schema\Table;

class SchemaTest extends \PHPUnit\Framework\TestCase
{
    public function testAddEmptyTable()
    {
        $subject = new Schema();
        $this->assertEquals(0, $subject->getTableCount());

        $table = $subject->add(new Table($name = 'tableName', $desc = 'the description') );
        $this->assertInstanceOf( 'LSS\Schema\Table', $table );
        $this->assertEquals( $table->getName(), $name );
        $this->assertEquals( $table->getDescription(), $desc );
        $this->assertEquals(1, $subject->getTableCount());
    }


    public function testIterator()
    {
        $subject = new Schema();
        $table = $subject->add(new Table( 'tableName', 'the description' ));
        $table2 = $subject->add( new Table( 'second_table', 'another description' ));
        $count = 0;
        /**
         * @var string $name
         * @var Table $table
         */
        foreach ($subject as $name => $table)
        {
            $this->assertEquals( $name, $table->getName() );
            $count++;
        }
        $this->assertEquals( $subject->getTableCount(), $count );
    }


    public function testQuoteIdentifier()
    {
        $this->assertEquals('', Schema::quoteIdentifier(''));
        $this->assertEquals(Schema::BACKTICK . 'abc' . Schema::BACKTICK, Schema::quoteIdentifier('abc'));
    }


    public function testQuoteDescription()
    {
        $this->assertEquals(Schema::QUOTE . Schema::QUOTE, Schema::quoteDescription(''));
        $this->assertEquals(Schema::QUOTE . 'abc' . Schema::QUOTE, Schema::quoteDescription('abc'));
        $this->assertEquals(Schema::QUOTE . 'abc' . Schema::QUOTE . Schema::QUOTE . 'def' . Schema::QUOTE, Schema::quoteDescription('abc' . Schema::QUOTE . 'def'));
    }


    public function testUnQuote()
    {
        $this->assertEquals( '', Schema::unQuote( '' ), '' );
        $this->assertEquals( 'no quotes', Schema::unQuote( 'no quotes' ) );
        $this->assertEquals( 'double quotes', Schema::unQuote( '"double quotes"' ) );
        $this->assertEquals( 'single quotes', Schema::unQuote( "'single quotes'" ) );
        $this->assertEquals( 'single don\'t quotes', Schema::unQuote( "'single don''t quotes'" ) );
    }
}
