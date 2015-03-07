<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    6 March 2015
 */

namespace LSS\Schema;

use LSS\Schema;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $table1 =
        "CREATE TABLE config (
          id mediumint(9) NOT NULL auto_increment,
          Name varchar(40) NOT NULL default '',
          MyValue varchar(100) NOT NULL default '',
          Description text NOT NULL,
          GroupID mediumint(9) NOT NULL default '0',
          FieldType varchar(30) NOT NULL default '',
          PRIMARY KEY  (id),
          KEY GroupID (GroupID)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 comment='hi there';";


    public function testRemoveTrailingComma()
    {
        $subject = new Parser();
        $this->assertEquals( '', $subject->removeTrailingComma( ', ,' ) );
        $this->assertEquals( '', $subject->removeTrailingComma( ' , ' ) );
        $this->assertEquals( 'blah blah', $subject->removeTrailingComma( 'blah blah' ) );
        $this->assertEquals( 'blah blah', $subject->removeTrailingComma( 'blah blah,' ) );
        $this->assertEquals( 'blah blah', $subject->removeTrailingComma( '   blah blah,  ' )  );
        $this->assertEquals( 'blah blah', $subject->removeTrailingComma( '   blah blah,,  ' ) );
    }


    public function testExtractComment()
    {
        $subject = new Parser();
        $this->assertEquals('', $subject->extractComment(''));
        $this->assertEquals('', $subject->extractComment('""'));
        $this->assertEquals('', $subject->extractComment('\'foo bar\''));
        $this->assertEquals('foo bar', $subject->extractComment('comment \'foo bar\''));
        $this->assertEquals('foo bar', $subject->extractComment('blah blah comment \'foo bar\' blah'));
        $this->assertEquals('foo \'bar', $subject->extractComment('comment \'foo \'\'bar\''));
    }


    public function testParseColumns()
    {
        $sql = "
          `Name` varchar(40) not null default '' comment 'hi there',
          `Description` mediumtext not null,
          `MyBoolean` tinyint(4) not null default \'0\'";

        $subject = new Parser();
        $table = new Table( 'x' );
        $table = $subject->parseColumns( $table, $sql );
        $this->assertEquals( $table->getColumnCount(), 3 );

        $one = $table->getColumnByName('Name');
        $this->assertEquals('Name', $one->getName());
        $this->assertEquals('hi there', $one->getDescription());
        $this->assertInstanceOf('LSS\Schema\Table\Column\String', $one);
        $this->assertEquals(40, $one->getLength());

        $two = $table->getColumnByName('Description');
        $this->assertEquals('Description', $two->getName());
        $this->assertEquals('', $two->getDescription());
        $this->assertInstanceOf('LSS\Schema\Table\Column\Text', $two);
        $this->assertEquals('medium', $two->getSize());

        $three = $table->getColumnByName('MyBoolean');
        $this->assertEquals('MyBoolean', $three->getName());
        $this->assertEquals('', $three->getDescription());
        $this->assertInstanceOf('LSS\Schema\Table\Column\Boolean', $three);
        $this->assertEquals('tiny', $three->getSize());
        $this->assertEquals(4, $three->getDigits());
    }


    public function testParseIndexes()
    {
        $sql = <<<TAG
          `id` int(10) not null auto_increment default '',
          `Name` varchar(40) not null default '',
          `Description` text not null,
          `GroupID` mediumint(9) not null default '0',
          `GroupID2` mediumint(9) not null default '0',
          PRIMARY  KEY (`id` ),
          KEY `Fred` (`GroupID`, `GroupID2` ),
          KEY `Bob` (`Name` )
TAG;
        $subject = new Parser();
        $table = new Table( 'x' );
        $table = $subject->parseColumns( $table, $sql );
        $this->assertEquals( 5, $table->getColumnCount() );
        $this->assertEquals( 3, $table->getIndexCount() );
        $iterator = $table->getIndexIterator();
        $iterator->rewind();
        $one = $iterator->current();
        $this->assertInstanceOf('LSS\Schema\Table\Index\Primary', $one);
        $this->assertEquals(['id'], $one->getColumns());

        $iterator->next();
        $two = $iterator->current();
        $this->assertInstanceOf('LSS\Schema\Table\Index', $two);
        $this->assertEquals(['GroupID', 'GroupID2'], $two->getColumns());
        $this->assertEquals('Fred', $two->getName());

        $iterator->next();
        $three = $iterator->current();
        $this->assertInstanceOf('LSS\Schema\Table\Index', $three);
        $this->assertEquals(['Name'], $three->getColumns());
        $this->assertEquals('Bob', $three->getName());
    }


    public function testFullTable()
    {
        $sql = <<<TAG
            CREATE TABLE `brand` (
              `id` mediumint(9) not null auto_increment COMMENT 'Unique brand identifier',
              `name` varchar(20) not null default '' COMMENT 'Name of the brand eg Onza',
              `logo` varchar(20) not null default '' COMMENT 'File name for the brand logo',
              `description` text not null COMMENT 'Content to appear on the Brands page',
              `LastModified` datetime not null default '0000-00-00 00:00:00' COMMENT 'Date and time this record was last changed',
              PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A list of brands (eg Kris Holm, UDC, Onza) which can be atta';
TAG;
        $subject = new Parser();
        $schema = new Schema();
        $subject->parse($schema, $sql . "\r\n" . $this->table1);
        $this->assertEquals(2, $schema->getTableCount());

        $one = $schema->getTable('brand');
        $this->assertEquals('Name of the brand eg Onza', $one->getColumnByName('name')->getDescription());
        $this->assertEquals('A list of brands (eg Kris Holm, UDC, Onza) which can be atta', $one->getDescription());
        $this->assertEquals(5, $one->getColumnCount());
        $this->assertEquals(1, $one->getIndexCount());

        $two = $schema->getTable('config');
        $this->assertEquals('hi there', $two->getDescription());
        $this->assertEquals(6, $two->getColumnCount());
        $this->assertEquals(2, $two->getIndexCount());
    }
}
