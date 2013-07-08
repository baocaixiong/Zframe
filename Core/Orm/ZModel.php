<?php
/**
 * ZModel class
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

use IteratorAggregate;
use ArrayAccess;
use Countable;
use Z\Core\ZCore;
use Z\Core\Orm\Exceptions\ZDbException;
use Z\Z;

class ZModel extends ZCore
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
     * 是否是一个新的model
     * 如果是TRUE，表示插入数据，否则表示更新
     * @var boolean
     */
    public $isNew = false;

    /**
     * 默认主键，约定俗成
     * @var int
     */
    protected $id;

    /**
     * 受到更改的数据
     * @var array
     */
    private $_modified = array();


    private static $_models;

    /**
     * 该model要保存的数据
     * @var array
     */
    protected $_attributes = array();

    protected $_notAttributes = array();

    /**
     * 每个Moel对象都拥有的一个对应的Table对象
     * @var \Z\Core\Orm\ZTable
     */
    protected $table;

    /** @access protected must be public because it is called from zTable */
    public function __construct() {
        $this->setIsNew(true);
        $tableClass = $this->tableClass;
        $this->table = $tableClass::getInstance(true);
        //$this->_attributes = array_flip($this->table->getTableSchema()->getColumns());

        $this->init();
        $this->attachBehaviors($this->behaviors()); //添加一个行为列表

        $this->afterConstruct();
    }
    
    public function __get($name)
    {
        if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        } elseif (isset($this->table->getTableSchema()->getColumns()[$name])) {
            return null;
        } else {
            echo '外键关系';
        }
    }

    public function __call($func, $params = array())
    {
        if (method_exists($this->table, $func)) {
            call_user_func_array(array($this->table, $func), $params);
        }
        //var_dump($this->table);
        return $this;
    }

    public function setAttributes($data, $saveNotAttributes = true)
    {
        if (!is_array($data)) {
            return ;
        }
        $attributes = $this->getAttributeNames();
        foreach ($data as $name => $value) {
            if (isset($attributes[$name])) {
                $this->_attributes[$name] = $value;
            } elseif ($saveNotAttributes) {
                $this->_notAttributes[$name] = $value;
            }
        }
    }

    public function setAttribute($name, $value)
    {
        if(property_exists($this,$name)) {
            $this->$name=$value;
        } elseif (isset($this->getAttributeNames()[$name])) {
            $this->_attributes[$name] = $value;
        } else {
            return false;
        }

        return true;
    }

    public function findByPk($pk)
    {
        $this->table->where('id  = ' . $pk);
        $this->setAttributes($this->table->execute());
        $this->isNew = false;
        return $this;
    }


    public function getAttributeNames()
    {
        return $this->table->getTableSchema()->getColumns();
    }

    /**
     * after construct model class
     * @return void
     */
    public function afterConstruct()
    {

    }
    /**
     * 初始化方法
     * @return void
     */
    public function init()
    {

    }

    /**
     * Model的行为类
     * @return array
     */
    public function behaviors()
    {
        return array();
    }

    /**
     * 获得本Model的Singleton
     *
     * @param boolean $force   是否强制获得一个新的Instance
     * @param boolean $replace 是否替换掉已有的Instance，如果没有会直接赋值
     * @return \Z\Core\Orm\ZModel
     */
    public static function getInstance($force = false, $replace = false)
    {
        $className = get_called_class();

        if(isset(self::$_models[$className]) && !$force) {
            return self::$_models[$className];
        } else {
            $model = new $className();

            if (is_null(self::$_models[$className]) || $replace) {
                self::$_models[$className] = $model;
            }

            $model->attachBehaviors($model->behaviors());
            return $model;
        }
    }

    /**
     * 获得一个新的ModelInstance
     * @param  boolean $replace 是否替换,默认为替换
     * @return \Z\Core\Orm\ZModel
     */
    public function getNewInstance($replace = true)
    {
        return self::getInstance(true, $replace);
    }
}
