<?php
/**
 * @file
 * @author Lightly Salted Software Ltd
 * @date   7 March 2015
 * @brief  Implements AugmentedTable
 */

namespace LSS\Schema;

use LSS\Schema\Table;
use LSS\Schema\Table\Column\ForeignKeyColumn;
use LSS\Schema\Table\Column\PrimaryKeyColumn;


/**
 * AugmentedTable
 *
 * Adds a whole lot of helper fields to the generic table class to give us shortcuts for a nice fluent syntax in the
 * Builder. Also defines standard field sizes where these are not specific to the domain.
 *
 * @package ISV\Generate\DatabaseSchema
 */
class AugmentedTable extends Table
{
    const KEY_SIZE = 'medium'; // for mediumint
    const KEY_DIGITS = 9; // for mediumint(9)


    /**
     * a primary key is usually 'id'
     * @param string $name        of field
     * @param string $description field comments
     * @return $this
     */
    public function addPrimaryKeyColumn($name = '', $description = '')
    {
        if (empty($name)) {
            $name = 'id';
        }
        if (empty($description)) {
            $description = 'Unique ' . $this->getName() . ' identifier';
        }
        $this->addColumn(new PrimaryKeyColumn($name, $description, false, self::KEY_SIZE, self::KEY_DIGITS));
        $this->addIndex(new Table\Index\Primary($name));

        return $this;
    }


    /**
     * @param string $foreignTable string table name of other table to link to (assumes there is an 'id' field which is the primary key of that table)
     * @param string $description  field comments
     * @param string $name name of column: defaults to 'id'
     * @return $this
     */
    public function addOneToOneKeyColumn($foreignTable, $description = '', $name = '')
    {
        if (empty($name)) {
            $name = 'id';
        }
        if (empty($description)) {
            $description = ForeignKeyColumn::RELATED_TEXT . ' ' . $foreignTable . '. ' . $description;
        }
        $this->addColumn(new ForeignKeyColumn($name, $description, false, self::KEY_SIZE, self::KEY_DIGITS, $foreignTable));
        $this->addIndex(new Table\Index\Unique($name));

        return $this;
    }


    /**
     * adds an indexed integer field linking this table with another.
     * The field name defaults to the foreign_table_name with suffix _id and assumes
     * the foreign key field is an integer. To add a string foreign key, call addStringField()
     * and addIndex().
     * @param $foreignTable string table name of other table to link to (assumes there is an 'id' field which is the primary key of that table)
     * @param $description  string defaults to '' unless id is also blank
     * @param $thisField    string name of field in this table
     * @return $this
     */
    public function addForeignKeyColumn($foreignTable, $description = '', $thisField = '')
    {
        if ($thisField == '') {
            $thisField = $foreignTable . '_id';
        }
        $description = trim(ForeignKeyColumn::RELATED_TEXT . ' ' . $foreignTable . ' ' . $description);
        $this->addColumn(new ForeignKeyColumn($thisField, $description, false, self::KEY_SIZE, self::KEY_DIGITS, $foreignTable));
        $this->addIndex(new Table\Index($thisField));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param string $description field comments
     * @return $this
     */
    public function addIntegerColumn($name, $description = '')
    {
        $this->addColumn(new Table\Column\IntegerColumn($name, $description, false, self::KEY_SIZE, self::KEY_DIGITS));

        return $this;
    }


    /**
     * add a column called display_order and create an index for it
     * @param string $description field comments
     * @return $this
     */
    public function addDisplayOrderColumn($description = 'lower values go to the top, 0 = sort alphabetically')
    {
        $this->addIntegerColumn('display_order', $description);
        $this->addIndex(new Table\Index('display_order'));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param string $description field comments
     * @return $this
     */
    public function addCurrencyColumn($name, $description = '')
    {
        return $this->addFloatColumn($name, 10, 2, $description);
    }


    /**
     * @param string $name        of field
     * @param int    $width
     * @param int    $decimalPlaces
     * @param string $description field comments
     * @return $this
     */
    public function addFloatColumn($name, $width, $decimalPlaces, $description = '')
    {
        $this->addColumn(new Table\Column\FloatColumn($name, $description, false, $width, $decimalPlaces));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param array  $values      valid values for the enumeration
     * @param string $description field comments
     * @return $this
     */
    public function addEnumerationColumn($name, array $values, $description = '')
    {
        $this->addColumn(new Table\Column\EnumerationColumn($name, $description, false, $values));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param array  $values      valid values for the enumeration
     * @param string $description field comments
     * @return $this
     */
    public function addSetColumn($name, array $values, $description = '')
    {
        $this->addColumn(new Table\Column\SetColumn($name, $description, false, $values));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param string $description field comments
     * @return $this
     */
    public function addDateTimeColumn($name, $description = '')
    {
        $this->addColumn(new Table\Column\DateTimeColumn($name, $description));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param string $description field comments
     * @return $this
     */
    public function addDateColumn($name, $description = '')
    {
        $this->addColumn(new Table\Column\DateColumn($name, $description));

        return $this;
    }


    /**
     * @param string $description field comments
     * @return $this
     */
    public function addLastModifiedColumn($description = 'Date and time this record was last changed')
    {
        return $this->addDateTimeColumn('last_modified', $description);
    }


    /**
     * @param string $description field comments
     * @return $this
     */
    public function addDateCreatedColumn($description = 'Date and time this record was first created')
    {
        return $this->addDateTimeColumn('date_created', $description);
    }


    /**
     * @param string $name        of field
     * @param string $description field comments
     * @return $this
     */
    public function addBooleanColumn($name, $description = '')
    {
        $this->addColumn(new Table\Column\BooleanColumn($name, $description));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param int    $length      number of characters in the field
     * @param string $description field comments
     * @return $this
     */
    public function addStringColumn($name, $length, $description = '')
    {
        assert($length > 0);
        $this->addColumn(new Table\Column\StringColumn($name, $description, false, $length));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param string $description field comments
     * @return $this
     */
    public function addTextColumn($name, $description = '')
    {
        $this->addColumn(new Table\Column\TextColumn($name, $description));

        return $this;
    }


    /**
     * @param string $name        of field
     * @param string $description field comments
     * @return $this
     */
    public function addMediumTextColumn($name, $description = '')
    {
        $this->addColumn(new Table\Column\TextColumn($name, $description, false, 'medium'));

        return $this;
    }


    /**
     * @param string $name
     * @param array  $columns
     * @param string $type
     * @return $this
     */
    public function addStandardIndex($name, $columns = [ ], $type = '')
    {
        $this->addIndex(new Table\Index($name, $columns, $type));

        return $this;
    }


    /**
     * @param string $name
     * @param array  $columns
     * @return $this
     */
    public function addUniqueIndex($name, $columns = [ ])
    {
        $this->addIndex(new Table\Index\Unique($name, $columns));

        return $this;
    }
}
