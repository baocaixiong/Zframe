<?php
/**
 * ZTable class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm;

use Z\Z;
use Z\Exceptions\ZDbException;
use Z\Core\Orm\Schema\ZColumnSchema;
use Z\Core\Orm\Schema\ZForeignKey;
use Z\Core\Orm\Schema\ZVirtualColumn;
use Z\Core\Orm\Schema\ZTableSchema;

abstract class ZTable extends ZOrmAbstract
{
    /*****EVENT INIT START*****/
    const EVENT_BEFORE_SAVE = 'onBeforeSave';

    const EVENT_AFTER_SAVE = 'onAfterSave';

    const EVENT_BEFORE_DELETE = 'onBeforeDelete';

    const EVENT_AFTER_DELETE = 'onAfterDelete';

    const EVENT_BEFORE_FIND = 'onBeforeFind';

    const EVENT_AFTER_FIND = 'onAfterFind';
    /*****EVENT INIT END*****/

    const PRIMARY_KEY = 'id';
    /**
     * 本表的实例对象
     * @var \Z\Core\Orm\ZTable
     */
    private static $_tableInstances = array();

    /**
     * 本类名称
     * @var string
     */
    private $_className;

    /**
     * table schema
     * @var \Z\Core\Orm\Scema\ZDbTableSchema
     */
    private $_schema;

    /**
     * 数据结果
     * @var array
     */
    protected $rows;

    /**
     * 要查询的字段，如果为空，应该是*
     * @var array
     */
    protected $select = array();

    /**
     * join on conditions
     * @var array
     */
    protected $conditions;

    /**
     * where条件
     * @var array
     */
    protected $where = array();

    /**
     * order by conditions
     * @var array
     */
    protected $order = array();

    /**
     * limit condition (0 ,1)
     * @var string
     */
    protected $limit;

    /**
     * offset condition 
     * @var string
     */
    protected $offset;

    /**
     * group by condition
     * @var string 
     */
    protected $group;

    /**
     * having condition 
     * @var string
     */
    protected $having;

    /**
     * 
     * @var string
     */
    protected $lock;

    protected $access;

    /**
     * CONSTRUCT METHOD
     * @access private
     * @return \Z\Core\Orm\ZTable
     * @throws ZDbException 如果__CLASS__::tableName为空
     */
    protected function __construct()
    {
        $this->init();
        if (empty($this->tableName) && !is_string($this->tableName)) {
            throw new ZDbException('Must declare the table name, and must a string value.');
        }

        $this->connection = Z::app()->getDb();
        $this->driverName = $this->connection->getDriverName();

        //$this->cache = $this->connection->getCache();
        $this->setTableSchema();
        $this->setPrimaryKey();
        $this->setColumns();
    }

    /**
     * set table schema
     *
     * @return \Z\Core\Orm\ZTable
     */
    public function setTableSchema()
    {
        $driverName = $this->connection->getDriverName();
        $prefx = $this->connection->getTablePrefix();

        if (strpos($this->tableName, $prefx) === false) {
            $realName = $prefx . $this->tableName;
        } else {
            $realName = $this->tableName;
        }

        $this->_schema = new ZTableSchema();
        $this->_schema->name = $realName;
        $this->_schema->rawName = '`' . $realName . '`';

        return $this;
    }

    /**
     * this table schema 
     * @return \Z\Core\Orm\Schema\ZTableSchema
     */
    public function getTableSchema()
    {
        return $this->_schema;
    }

    /**
     * 设置主键
     * @return \Z\Core\Orm\ZTable
     */
    public function setPrimaryKey()
    {
        $this->setColumn(self::PRIMARY_KEY, 'int', 1, array(
            'isPrimaryKey'  => true,
            'autoIncrement' => true,
            'allowNull'     => false
        ));

        $this->getTableSchema()->setPrimaryKey($this->getTableSchema()->getColumn(self::PRIMARY_KEY));
        return $this;
    }

    /**
     * 设置本表字段
     * @param string $columnName columnName
     * @param string $type       PHP type
     * @param mixed  $default    default value
     * @param array  $options    column options
     */
    protected function setColumn($columnName, $type, $default = null, $options = array())
    {
        $hasDefault = func_num_args() >= 3;

        $this->getTableSchema()->columns[$columnName] = new ZColumnSchema(
            $columnName, $type, $default, $options
        );

        return $this;
    }
    
    /**
     * 设置外键
     * @param  string             $name          foreign key name
     * @param  string             $selfField     this table field
     * @param  \Z\Core\Orm\ZTable $relatedTable  relation table instance
     * @param  string             $relatedField  relation table field
     * @return \Z\Core\Orm\ZTable
     */
    public function foreignKey($name, $selfField, $relatedTable, $relatedField = self::PRIMARY_KEY)
    {
        if (!array_key_exists($selfField, $this->getTableSchema()->columns)) {
            throw new ZDbException("Invalid field '{$selfField}' in " . __CLASS__);
        }

        $this->getTableSchema()->foreignKeys[$keyName] = new ZForeignKey($selfField, $relatedTable, $relatedField);

        return $this;
    }

    /**
     * 创建一个虚拟字段
     * @param  string $virtualFieldName virtual field name
     * @param  string $foreignKeyName   foreign key instance name
     * @param  string $relateeField     relation field
     * @return \Z\Core\Orm\ZTable
     */
    public function virtualField($virtualFieldName, $foreignKeyName, $relateeField = '')
    {
        if (!array_key_exists($foreignKeyName, $this->getTableSchema()->foreignKeys)) {
            throw new ZDbException("ForeignKey '{$foreignKey} is not exists.");
        }

        empty($relateeField) && $relateeField = $virtualFieldName;

        $this->getTableSchema()->virtualColumns[$virtualFieldName] = new ZVirtualColumn(
            $this->getTableSchema()->foreignKeys[$foreignKeyName],
            $relateeField
        );

        return $this;
    }

    /**
     * 设置Table的字段和外键关系
     */
    abstract function setColumns();

    /**
     * 要查询的字段
     * @param  string $columns 要查询的字段，可以是多个
     * @return \Z\Core\Orm\ZTable
     */
    public function select($columns)
    {
        $this->__destruct();
        foreach (func_get_args() as $columns) {
            $this->select[] = $columns;
        }
        return $this;
    }

    public function 

    /**
     * DESTRUCT METHOD
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->connection->cache && !$this->select && !empty($this->rows)) {
            $access = $this->access;
            if (is_array($access)) {
                $access = array_filter($access);
            }
            $this->connection->cache->set("$this->tableName;" . implode(",", $this->conditions), $access);
        }

        $this->select = null;
        $this->rows = null;
    }

    /**
     * 初始化方法，比如可以再这里on开发者者自己定义的事件句柄
     * @return void
     */
    public function init()
    {

    }

    /**
     * 获得本Table的Singleton
     * @return \Z\Core\Orm\ZTable
     */
    public static function getInstance()
    {
        $class = get_called_class();

        if (!array_key_exists($class, self::$_tableInstances)) {
            self::$_tableInstances[$class] = new $class();
            self::$_tableInstances[$class]->_className = $class;
        }

        return self::$_tableInstances[$class];
    }

    /**
     * 防止Table类被clone
     * @return void
     * @throws ZDbException
     */
    public function __clone()
    {
        throw new ZDbException("Clone {$this->_className} is not allowed.");
    }
}