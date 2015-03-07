<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema;

use LSS\Schema\Table\ColumnFactory;
use LSS\Schema\Table\Index;
use LSS\Schema;


/**
 * Class Parser
 * @package LSS\Schema
 */
class Parser
{
    /** @var array */
    private $ignoredTables = [ ];

    /** @var ColumnFactory */
    private $columnFactory;


    /**
     * @param array                $ignoreTables
     * @param ColumnFactory | null $columnFactory
     */
    public function __construct($ignoreTables = [ ], $columnFactory = null)
    {
        $this->ignoredTables = $ignoreTables;
        if (empty($columnFactory)) {
            $columnFactory = new ColumnFactory();
        }
        $this->columnFactory = $columnFactory;
    }


    /**
     * Schema is a parameter so you can parse several snippets of sql incrementally
     * @param Schema $schema where to add the parsed tables.
     * @param string $sql    string containing sql to be parsed: should be in format as produced by phpMyAdmin export
     *                       oops! what if an sql comment has a ; in it?
     *                       match end of line perhaps?
     */
    public function parse(Schema $schema, $sql)
    {
        $tables = preg_split('/;[\r\n]/ms', $sql, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($tables as $tableSQL) {
            $tableSQL = trim($tableSQL);
            if (strlen($tableSQL) <= 0) {
                continue;
            }
            $table = $this->parseTable($tableSQL);
            if (!empty($table)) {
                $schema->add($table);
            }
        }
    }


    /**
     * assumes each column is on its own line, with the CREATE TABLE and
     * table type info also on their own lines, as returned by MySQL
     * @param string $tableSql
     * @return Table|null
     * @throws \Exception
     */
    public function parseTable($tableSql)
    {
        // break the sql into first line, columns, last line.
        //if (!preg_match( '/\s*create\s+table\s+([^\s+]*)\s+\((.*)\n\s*\)\s*([^\)]*)\s*$/ims', $tableSql, $matches ))
        if (!preg_match('/\s*create\s+table\s+([^\s+]*)\s+\((.*)\s*\n\s*\)\s*(.*)\s*$/ims', $tableSql, $matches)) {
            throw new \Exception('Invalid sql ' . $tableSql);
        }
        $name = Schema::unQuote($matches[1]);
        if (in_array($name, $this->ignoredTables)) {
            return null;
        }

        $table = new Table($name, $this->extractComment($matches[3]));
        $this->parseColumns($table, $matches[2]);
        return $table;
    }


    /**
     *
     * known limitation: one column per line: a \n is required between each column definition
     * known limitation: single field primary key
     * @param Table  $table
     * @param string $columnText
     * @return Table
     */
    public function parseColumns(Table $table, $columnText)
    {
        // remove stuff we don't want to parse or don't currently care about
        $columnText = preg_replace('/\s+default\s*\'\'/ims', '', $columnText);
        $columnText = preg_replace('/\s+default\s*\'[-0:. ]+\'/ims', '', $columnText);
        // later: add charset stuff in here

        $columns = preg_split('/[\r\n]+\s*/m', $columnText, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($columns as $column) {
            $column  = $this->removeTrailingComma($column);
            $comment = $this->extractComment($column);
            $column  = preg_replace('/\s+comment\s*\'.*\'/im', '', $column); // snip off the comment

            if (preg_match('/^PRIMARY\s+KEY\s+\(\s*(.*)\s*\)/im', $column, $fields)) {
                assert(strpos(',', $fields[1]) === false); // known limitation: single field primary key
                $table->addIndex(new Index\Primary(Schema::unQuote(trim($fields[1]))));
            } else if (preg_match('/^UNIQUE KEY\s+(.*)\s+\(\s*([^\s]+)(?:\s*,\s*([^\s]+))*\s*\)/im', $column, $fields)) {
                array_shift($fields); // delete $fields[0] coz it is the whole string
                $fields = array_map('LSS\Schema::unQuote', $fields);
                $name   = array_shift($fields);
                $table->addIndex(new Index\Unique($name, $fields));
            } else if (preg_match('/^FULLTEXT KEY\s+(.*)\s+\(\s*([^\s]+)(?:\s*,\s*([^\s]+))*\s*\)/im', $column,
                $fields)) {
                array_shift($fields); // delete $fields[0] coz it is the whole string
                $fields = array_map('LSS\Schema::unQuote', $fields);
                $name   = array_shift($fields);
                $table->addIndex(new Index($name, $fields, 'fulltext'));
            } else if (preg_match('/^KEY\s+(.*)\s+\(\s*([^\s]+)(?:\s*,\s*([^\s]+))*\s*\)/im', $column, $fields)) {
                array_shift($fields); // delete $fields[0] coz it is the whole string
                $fields = array_map('LSS\Schema::unQuote', $fields);
                $name   = array_shift($fields);
                $table->addIndex(new Index($name, $fields));
            } else {
                // ordinary field: name is first word (optionally quoted), data type is rest of string
                preg_match('/^([^\s]+)\s+(.*)\s*/', $column, $matches);
                $name = Schema::unQuote($matches[1]);
                $table->addColumn($this->columnFactory->create($name, $comment, $matches[2]));
            }
        }

        return $table;
    }


    /**
     * snip off optional trailing comma and white space
     * @param $sql string to trim
     * @return  string
     */
    public function removeTrailingComma($sql)
    {
        return trim($sql, ' ,');
    }


    /**
     * assumes the comment is single quote enclosed at the end of the
     * string. Replaces SQL escaping of single quotes, which replaces a single
     * quote with two eg ' becomes ''
     * Note that comments on the table are "comment='my comment'" and comments
     * on the column are "comment 'my comment'" (no equals sign). Doh.
     * @param string $sql string containing de-escaped comment
     * @return string comment text
     */
    public function extractComment($sql)
    {
        if (preg_match('/comment\s*(?:=)?\s*\'(.*)\'/im', $sql, $comment)) {
            return Schema::unQuote($comment[1]);
        }

        return '';
    }
}
