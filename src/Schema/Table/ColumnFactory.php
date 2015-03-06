<?php
/**
 * @file
 * @author  Lightly Salted Software Ltd
 * @date    March 2015
 */

namespace LSS\Schema\Table;

/**
 * Class ColumnFactory
 */
class ColumnFactory
{
    // maps sql type regex to class names
    private $map = [
        '([\a-z]*)int\(([\d]+)\) not null auto_increment' => '\LSS\Schema\Table\Column\PrimaryKey',
        'tinyint\(([\d]+)\)'                              => '\LSS\Schema\Table\Column\Boolean',
        '([\a-z]*)int\(([\d]+)\)'                         => '\LSS\Schema\Table\Column\Integer',
        '([\a-z]*)text'                                   => '\LSS\Schema\Table\Column\Text',
        'datetime'                                        => '\LSS\Schema\Table\Column\DateTime',
        'date'                                            => '\LSS\Schema\Table\Column\Date',
        'varchar\s*\((\d+)\)'                             => '\LSS\Schema\Table\Column\String',
        'enum\s*\(\s*(.*)\s*\) '                          => '\LSS\Schema\Table\Column\Enumeration',
        'set\s*\(\s*(.*)\s*\) '                           => '\LSS\Schema\Table\Column\Set',
        'decimal\s*\(\s*(\d+)\s*,\s*(\d+)\s*\)'           => '\LSS\Schema\Table\Column\Float',
    ];


    /**
     *
     * @param string $sqlType regular expression
     * @param string $className
     * @return $this
     */
    public function setMapType($sqlType, $className)
    {
        $this->map[strtolower($sqlType)] = $className;

        return $this;
    }


    /**
     * @param string $name        sql column name
     * @param string $description textual description for comment field of column
     * @param string $sqlType     eg varchar(9)
     * @return Column
     */
    public function create($name, $description, $sqlType)
    {
        $sqlType = strtolower($sqlType);
        foreach ($this->map as $regex => $className) {
            if (!preg_match('/' . $regex . '/i', $sqlType, $matches)) {
                continue;
            }
            $allowNull = strpos($sqlType, ' not null') === false;

            $instance = new $className($name, $description, $allowNull,
                isset($matches[1]) ? $matches[1] : null,
                isset($matches[2]) ? $matches[2] : null
            );

            return $instance;
        }
        throw new \InvalidArgumentException('Unknown SQL field type "' . $sqlType . '" for field ' . $name);
    }
}