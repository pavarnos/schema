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
        '([a-z]*)int\(([\d]+)\) not null auto_increment' => '\LSS\Schema\Table\Column\PrimaryKeyColumn',
        'tinyint\(([\d]+)\)'                              => '\LSS\Schema\Table\Column\BooleanColumn',
        '([a-z]*)int\(([\d]+)\)'                         => '\LSS\Schema\Table\Column\IntegerColumn',
        '([a-z]*)text'                                   => '\LSS\Schema\Table\Column\TextColumn',
        'json'                                            => '\LSS\Schema\Table\Column\JsonColumn',
        'datetime'                                        => '\LSS\Schema\Table\Column\DateTimeColumn',
        'date'                                            => '\LSS\Schema\Table\Column\DateColumn',
        'varchar\s*\((\d+)\)'                             => '\LSS\Schema\Table\Column\StringColumn',
        'enum\s*\(\s*([^\)]*)\s*\) '                      => '\LSS\Schema\Table\Column\EnumerationColumn',
        'set\s*\(\s*(.*)\s*\) '                           => '\LSS\Schema\Table\Column\SetColumn',
        'decimal\s*\(\s*(\d+)\s*,\s*(\d+)\s*\)'           => '\LSS\Schema\Table\Column\FloatColumn',
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
        foreach ($this->map as $regex => $className) {
            if (!preg_match('/^' . $regex . '/i', $sqlType, $matches)) {
                continue;
            }
            $allowNull = stripos($sqlType, ' not null') === false;

            $instance = new $className($name, $description, $allowNull,
                isset($matches[1]) ? $matches[1] : null,
                isset($matches[2]) ? $matches[2] : null
            );

            return $instance;
        }
        throw new \InvalidArgumentException('Unknown SQL field type "' . $sqlType . '" for field ' . $name);
    }
}
