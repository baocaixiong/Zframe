<?php
/**
 * ZQueryBuilder class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Queries\Mysql
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Queries;

use Z\Core\Orm\Exceptions\ZAliasConflictException;
use Z\Core\Orm\Exceptions\ZUndefinedColumnException;
use TableInterface;

class ZSelect extends ZQuery
{
    /**
     * WHERE过滤条件准则
     * @var \Z\Core\Orm\Queries\ZCriteria
     */
    protected $criteria;

    /**
     * 要查询的字段
     * 作为一个索引数组。数组的键是alias，值是ColumnName
     * @var array
     */
    protected $fields = array();

    /**
     * SQL ORDER BY
     * @var string
     */
    protected $order = '';

    /**
     * SQL GROUP BY
     * @var string
     */
    protected $group = '';

    /**
     * HAVING 过滤条件
     * @var \Z\Core\Orm\Queries\ZCriteria
     */
    protected $having;

    /**
     * CONSTRUCT METHOD
     * 创建一个SelectQuery的实例
     * @param \Z\Core\Orm\ZTable $table  [description]
     * @param string             $fields [description]
     * @param string             $option [description]
     */
    public function __construct(TableInterface $table, $fields = '*', $option = '')
    {
        $this->table = $table;
        $this->tableSchema = $table->getTableSchema();

    }

    public function fields($fields = '*', $option = '')
    {
        $n = func_num_args();

        if ($n > 1) {
            $fields = func_get_args();
        }

        is_string($fields) && $fields= array($fields) ;
        foreach ($fields as $key => $value) {
            $columnName = is_numeric($key) ? $value : $key;
            $aliasName = $value;
            $this->_addField($columnName, $aliasName);
        }

        return $this;
    }


    /**
     * 添加Select字段和别名
     * @param string $columnName column name
     * @param string $aliasName  alias name
     */
    public function _addField($columnName, $aliasName)
    {
        if (isset($this->fields[$aliasName])) {
            throw new ZAliasConflictException('重复的Column别名:' . $aliasName);
        }

        if (!array_key_exists($columnName, $this->tableSchema->columns)) {
            throw new ZUndefinedColumnException('未知的列名 ' . $this->tableSchema->rawName . ':' . $columnName);
        }

        $this->fields[$aliasName] = $this->tableSchema->getColumn($columnName);
    }


    public function __toString()
    {
        $queryString = 'SELECT ';

        $tableName = $this->tableSchema->rawName;
        if (empty($this->fields)) {
            $queryString .= $tableName . '.*';
        } else {
            $tempString = array();
            foreach ($this->fields as $alias => $column) {
                $tempString[] = $tableName . '.' . $column->rawName . ' AS `' . $alias . '`';   
            }
            $queryString .= implode(', ', $tempString);
        }
    
        $queryString .= "\nFROM";

        $queryString .= "\n$tableName";

        $queryString .= "\nWHERE";


        return $queryString;
    }
}
