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
use TableInterface;

abstract class ZTable extends ZOrmAbstract implements TableInterface
{
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
     * 主键
     * @var string
     */
    public $primaryKey = 'id';


    /**
     * 引用的Table
     * @var array
     */
    public $referencingTable = array();
    
    /**
     * 引用的Column
     * @var array
     */
    public $referencingColumn = array();

    /**
     * 被引用的列
     * @var array
     */
    public $referencedColumn = array();

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

        if (empty($this->modelClass) && !is_string($this->modelClass)) {
            throw new ZDbException('Must declare the modelClass name, and must a string value.');
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
    protected function setTableSchema()
    {
        $this->_schema = new ZTableSchema();
        $this->_schema->name = $this->tableName;
        $this->_schema->rawName = $this->connection->getTableRawName($this->tableName);

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
    protected function setPrimaryKey()
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
     * @param string $alias      column alias name
     */
    protected function setColumn($columnName, $type, $default = null, $options = array(), $alias = '')
    {
        $hasDefault = func_num_args() >= 3;

        $this->getTableSchema()->columns[$columnName] = new ZColumnSchema(
            $columnName, $type, $default, $options, $alias
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
    protected function foreignKey($name, $selfField, $relatedTable, $relatedField = self::PRIMARY_KEY)
    {
        if (!array_key_exists($selfField, $this->getTableSchema()->columns)) {
            throw new ZDbException("Invalid field '{$selfField}' in " . __CLASS__);
        }

        $this->referencingTable[$relatedTable->getTableSchema()->name] = $relatedTable;
        $this->referencingColumn[$relatedTable->getTableSchema()->name] = $relatedTable->getTableSchema()->getColumn($relatedField);

        $this->referencedColumn[$relatedTable->getTableSchema()->name] = $this->getTableSchema()->getColumn($selfField);

        $this->getTableSchema()->foreignKeys[$name] = new ZForeignKey($selfField, $relatedTable, $relatedField);

        return $this;
    }

    /**
     * 创建一个虚拟字段
     * @param  string $virtualFieldName virtual field name
     * @param  string $foreignKeyName   foreign key instance name
     * @param  string $relateeField     relation field
     * @return \Z\Core\Orm\ZTable
     */
    protected function virtualColumns($virtualFieldName, $foreignKeyName, $relateeField = '')
    {
        if (!array_key_exists($foreignKeyName, $this->getTableSchema()->foreignKeys)) {
            throw new ZDbException("ForeignKey '{$foreignKeyName}' is not exists.");
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
     * 初始化方法，比如可以再这里on开发者者自己定义的事件句柄
     * @return void
     */
    public function init()
    {

    }

     /**
     * 获得本Table的Singleton
     * @param boolean $force 是否强制获取到一个新的类
     * @return \Z\Core\Orm\ZTable
     */
    public static function getInstance($force = false)
    {
        $class = get_called_class();

        if (!array_key_exists($class, self::$_tableInstances) || $force) {
            self::$_tableInstances[$class] = new $class();
            self::$_tableInstances[$class]->_className = $class;
        }

        return self::$_tableInstances[$class];
    }

    public function select($fields = '', $option = '')
    {
        $queryClassName = $this->_createQueryClassName('select');
        /**
         * query instance
         * @var \Z\Core\Orm\Queries\ZSelect
         */
        return new $queryClassName($this, $fields, $option);
    }

    /**
     * 创建一个queryInstance
     * @param string $model 当前数据库操作模式
     * like: select update insert delete等等
     * @return 
     */
    private function _createQueryClassName($model)
    {
        $driverName = $this->connection->getDriverName();

        if (isset($this->_queryClassMap[$driverName]) && isset($this->_queryClassMap[$driverName][$model])) {
            $queryClassName = $this->_queryNamespace . '\\' . $this->_queryClassMap[$driverName][$model];
        } else {
            $queryClassName = $this->_queryNamespace . '\\Z' . ucfirst($model);
        }

        return $queryClassName;
    }

    private $_queryNamespace = 'Z\Core\Orm\Queries';

    private $_queryClassMap = array(
        'mysql' => array(
            'select' => 'Mysql\ZSelect',
        )
    );
}