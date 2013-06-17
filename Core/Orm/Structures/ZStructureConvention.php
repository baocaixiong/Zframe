<?php
/**
 * ZStructureConvention class
 * from NotORM
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Structures
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Structures;

class ZStructureConvention implements ZStructureInterface
{
    /**
     * table primary key
     * @var string
     */
    protected $primaryKey;

    /**
     * table foreign key
     * @var string
     */
    protected $foreignKey;

    /**
     * table name
     * @var string
     */
    protected $tableName;

    /**
     * table prefix e.g: like 'tbl_'
     * @var string
     */
    protected $tablePrefix;

    /**
     * CONSTRUCT METHOD
     * @param string $primaryKey  table primary key
     * @param string $foreignKey  table foreign key
     * @param string $tableName   table name
     * @param string $tablePrefix table prefix
     */
    public function __construct($primaryKey = 'id', $foreignKey = '%s_id', $tableName = '%s', $tablePrefix = '')
    {
        $this->primaryKey = $primaryKey;
        $this->foreignKey = $foreignKey;
        $this->tableName = $tableName;
        $this->tablePrefix = $tablePrefix;
    }


    public function getPrimary($tableName)
    {
        return sprintf($this->primaryKey, $this->getColumnFromTable($tableName));
    }
    
    public function getReferencingColumn($name, $tableName)
    {
        return $this->getReferencedColumn(substr($tableName, strlen($this->tablePrefix)), $this->tablePrefix . $name);
    }
    
    public function getReferencingTable($name, $tableName)
    {
        return $this->tablePrefix . $name;
    }
    
    public function getReferencedColumn($name, $tableName)
    {
        return sprintf(
            $this->foreignKey, $this->getColumnFromTable($name), substr($tableName, strlen($this->tablePrefix))
        );
    }
    
    public function getReferencedTable($name, $tableName)
    {
        return $this->tablePrefix . sprintf($this->tableName, $name, $tableName);
    }
    
    public function getSequence($tableName)
    {
        return null;
    }
    
    protected function getColumnFromTable($name)
    {
        if ($this->tableName != '%s'
            && preg_match('(^' . str_replace('%s', '(.*)', preg_quote($this->tableName)) . '$)', $name, $match)) {
            return $match[1];
        }
        return $name;
    }
}