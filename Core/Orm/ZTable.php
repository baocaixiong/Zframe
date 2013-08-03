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
use ZTableInterface;
use Z\Core\ZCore;
use Z\Core\Orm\Exceptions\ZDbPrepareFailedException;

abstract class ZTable extends ZCore implements ZTableInterface
{
    const PRIMARY_KEY = 'id';

    /**
     * 数据库连接
     * @var \Z\Core\Orm\ZDbConnection
     */
    public $connection;

    /**
     * 数据库驱动名称
     * @var string
     */
    public $driverName;

    /**
     * 缓存
     * @var \Z\Caching\ZCacheAbstract
     */
    protected $cache;

    /**
     * table name
     * 如果有tablePrefix的话，此name为不含tablePrefix的table名
     * @var string
     */
    protected $tableName;

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
     * PDOStatement
     * @var \PDOStatement
     */
    protected $statement;

    protected $query;

    protected $lastQuery;

    private $_queryNamespace = 'Z\Core\Orm\Queries';

    private $_queryClassMap = array(
        'mysql' => array(
            'select' => 'Mysql\ZSelect',
            //'join' => '\Mysql\ZJoin'
        )
    );

    private $_fetchMode = array();



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
    }

    public function createNewInstance()
    {
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
        $this->_schema->rawName = $this->_schema->getTableRawName($this->tableName);

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
        //$hasDefault = func_num_args() >= 3;

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

        $this->getTableSchema()->foreignKeys[$name] = new ZForeignKey($selfField, $relatedTable, $name, $relatedField);

        return $this;
    }

    /**
     * 创建一个虚拟字段
     * @param  string $virtualFieldName virtual field name
     * @param  string $foreignKeyName   foreign key instance name
     * @param  string $relatedField     relation field
     * @return \Z\Core\Orm\ZTable
     */
    protected function virtualColumn($virtualFieldName, $foreignKeyName, $relatedField = '')
    {
        if (!array_key_exists($foreignKeyName, $this->getTableSchema()->foreignKeys)) {
            throw new ZDbException("ForeignKey '{$foreignKeyName}' is not exists.");
        }

        empty($relatedField) && $relatedField = $virtualFieldName;

        $this->getTableSchema()->virtualColumns[$virtualFieldName] = new ZVirtualColumn(
            $this->getTableSchema()->foreignKeys[$foreignKeyName],
            $this->getTableSchema()->foreignKeys[$foreignKeyName]->getTable()->getTableSchema(),
            $relatedField
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
            self::$_tableInstances[$class]->createNewInstance();
            self::$_tableInstances[$class]->_className = $class;
        }

        return self::$_tableInstances[$class];
    }

    
    public function select($fields = array())
    {
        $queryClassName = $this->createQueryClassName('select');
        /**
         * query instance
         * @var \Z\Core\Orm\Queries\ZSelect
         */
        return $this->query = new $queryClassName($this, $fields);
    }

    public function getRawTableName()
    {
        return $this->_schema->rawName;
    }


    /**
     * 创建一个queryInstance
     * @param string $model 当前数据库操作模式
     * like: select update insert delete等等
     * @return 
     */
    protected function createQueryClassName($model)
    {
        $driverName = $this->connection->getDriverName();

        if (isset($this->_queryClassMap[$driverName]) && isset($this->_queryClassMap[$driverName][$model])) {
            $queryClassName = $this->_queryNamespace . '\\' . $this->_queryClassMap[$driverName][$model];
        } else {
            $queryClassName = $this->_queryNamespace . '\\Z' . ucfirst($model);
        }

        return $queryClassName;
    }

    /**
     * 获得queries的名字空间全名
     * @return string
     */
    public function getQueryNamespace()
    {
        return $this->_queryNamespace;
    }

    /**
     * 执行一条sql语句
     * @return \PDOStatement
     */
    public function fetchAll($fetchAssociative = 2, $params = array())
    {
        return $this->_query('fetchAll', $fetchAssociative, $params);
    }

    public function fetchOne($fetchAssociative = 2, $params = array())
    {
        return $this->_query('fetch', $fetchAssociative, $params);
    }

    public function query($params = array())
    {
        return $this->_query('', 0, $params);
    }

    private function _query($method, $mode, $params = array())
    {
        try {
            $this->prepare();
            if ($params === array()) {
                $this->statement->execute();
            } else {
                $this->statement->execute($params);
            }

            if (empty($method)) {
                $result = new ZDataReader($this);
            } else {
                $mode = (array)$mode;
                call_user_func_array(array($this->statement, 'setFetchMode'), $mode);
                $result = $this->statement->$method();
                $this->statement->closeCursor();
            }
            return $result;
        } catch (\Exception $e) {
            throw new ZDbException('执行SQL语句错误,SQL: '. $this->lastQuery);
            
        }
    }

    public function prepare()
    {
        if ($this->statement === null) {
            try {
                $this->lastQuery = (string)$this->query;
                //var_dump($this->lastQuery);
                $this->statement = $this->connection->pdo->prepare($this->lastQuery);
            } catch (\Exception $e) {
                throw new ZDbPrepareFailedException('预准备语句处理失败,SQL: ' . $this->query);
            }
        }
    }

    public function setFetchMode($mode)
    {
        $params = func_get_args();
        $this->_fetchMode = $params;
        return $this;
    }

    /**
     * 获得PDOStatement对象，
     * @return \PDOStatement
     */
    public function getPdoStatement()
    {
        return $this->statement;
    }

    public function getLastQuery()
    {
        return $this->lastQuery;
    }
}