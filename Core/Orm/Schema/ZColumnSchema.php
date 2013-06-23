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
use Z\Core\Orm\ZDbConnection;

class ZColumnSchema extends ZObject
{
    /**
     * 列名称,不包括引号
     * @var string
     */
    public $name;

    /**
     * 列的名字，包括引号。可以来组建SQL
     * @var string
     */
    public $rawName;

    /**
     * 是否允许为空
     * @var boolean
     */
    public $allowNull = true;

    /**
     * 此列在数据库中的Type
     * @var string
     */
    public $dbType;

    /**
     * 此列在php中的Type
     * @var string
     */
    public $phpType;

    /**
     * default value
     * @var mixed
     */
    public $default;

    /**
     * 最大长度
     * @var int
     */
    public $maxlength;

    /**
     * 列的数据的精度，如果是一个数字的话
     * @var int
     */
    public $precision;

    /**
     * 是否为主键
     * @var boolean
     */
    public $isPrimaryKey = false;

    /**
     * 是否为外键
     * @var boolean
     */
    public $isForeignKey;

    /**
     * 是否为主键自增
     * @var boolean
     */
    public $autoIncrement = false;

    /**
     * column options
     * @var array
     */
    public $options = array();

    /**
     * CONSTRUCT METHOD
     * @param string $column  column name
     * @param string $type    php type
     * @param mixed  $default default value
     * @param array  $options options 
     */
    public function __construct($column, $type, $default = null, $options = array())
    {
        $this->name = $column;
        $this->rawName = '`' . $column . '`';

        $this->phpType = $type;

        $this->default = $default;

        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                unset($options[$key]);
            }
        }

        $this->options = $options;
    }

    public function createFieldValidator($phpType)
    {
        
    }
}