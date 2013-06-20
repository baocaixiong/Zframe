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

class ZDbScheme extends ZObject
{
    /**
     * table name
     * @var string
     */
    public $name;

    /**
     * table的真实名字，有时候和$name相同
     * @var string
     */
    public $rawName;

    /**
     * table 的主键
     * @var string
     */
    public $primaryKey;

    /**
     * 外键列表
     * @var array
     */
    public $foreignKeys = array();

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
     * 获得某个列的元数据
     * 如果存在，返回一个ZDbColumnSchema实例，否则返回Null
     * @param  string $column column name
     * @return mixed
     */
    public function getColumn($column)
    {
        return isset($this->columns[$name]) ? $this->columns[$name] : null;
    }

    /**
     * 返回表中所有字段
     * @return array
     */
    public function getColumns()
    {
        return array_keys($this->columns);
    }
}