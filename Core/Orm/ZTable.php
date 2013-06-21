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
    /**
     * 本表的实例对象
     * @var \Z\Core\Orm\ZTable
     */
    private static $_tableInstances = array();
    /**
     * 表的真实名称
     * @var string
     */
    private $_rawTableName;

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
     * column schemata
     * 如果不为空，那么数组的每个值都是\Z\Core\Orm\Scema\ZDbColumnSchema的实例
     * @var array 
     */
    private $_columnSchemata;

    /**
     * 要查询的字段，如果为空，应该是*
     * @var array
     */
    protected $select = array();

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
        
        $this->_rawTableName = $this->connection->getTablePrefix() . $this->tableName;

        $this->setFields();

        $this->setSchema();
    }



    /**
     * 设置Table的字段和外键关系
     */
    abstract function setFields();

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

    /**
     * 获得table的真实表名称
     * @return string
     */
    public function getRawTableName()
    {
        return $this->_rawTableName;
    }
}