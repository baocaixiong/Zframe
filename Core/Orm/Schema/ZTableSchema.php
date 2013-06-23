<?php
/**
 * ZStructureConvention class
 * from NotORM
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Schema
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Schema;

use Z\Core\ZObject;

class ZTableSchema extends ZObject
{
    /**
     * table name
     * @var string
     */
    public $name;

    /**
     * 能够用来组装SQL 加了反引号
     * @var string
     */
    public $rawName;

    /**
     * 外键列表
     * @var array
     */
    public $foreignKeys = array();

    /**
     * 虚拟字段
     * @var array
     */
    public $virtualColumns = array();

    /**
     * 本表的所有的列Columns的元数据
     * 数组的每个值应该为ZDbColumnSchema
     * @var array
     */
    public $columns=array();

    /**
     * 本表作为主表，被引用作为外键的字段列表
     * @var array
     */
    public $referenced = array();

    /**
     * table 的主键
     * @var \Z\Core\Orm\Schema\ZColumnSchema
     */
    private $_primarykey;

    /**
     * 获得某个列的元数据
     * 如果存在，返回一个ZDbColumnSchema实例，否则返回Null
     * @param  string $column column name
     * @return mixed
     */
    public function getColumn($column)
    {
        return isset($this->columns[$column]) ? $this->columns[$column] : null;
    }

    /**
     * 返回表中所有字段
     * @return array
     */
    public function getColumns()
    {
        return array_keys($this->columns);
    }

    /**
     * setPrimaryKey
     * @param \Z\Core\Orm\Schema\ZColumnSchema $column [description]
     */
    public function setPrimaryKey(ZColumnSchema $column)
    {
        $this->_primarykey = $column;
    }

    /**
     * get table primary key
     * @return null|\Z\Core\Orm\Schema\ZColumnSchema
     */
    public function getPrimaryKey()
    {
        if (empty($this->_primarykey)) {
            foreach ($this->columns as $key => $value) {
                if ($value->isPrimaryKey) {
                    $this->_primarykey = $value;
                }
            }
        }

        return $this->_primarykey;
    }
}