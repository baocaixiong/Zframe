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

class ZModel implements IteratorAggregate, ArrayAccess, Countable
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


    protected $row, $zTable;

    /** @access protected must be public because it is called from zTable */
    public function __construct(array $row, ZTable $zTable, $idNew = false) {
        foreach ($row as $key => $value) {
            $this->$key = $value;
        }

        $this->row = $row;
        $this->zTable = $zTable;
    }
    
    /** Get primary key value
    * @return string
    */
    public function __toString() {
        return (string) $this[$this->zTable->getTableSchema()->getPrimaryKey()]; // (string) - PostgreSQL returns int

    }
    
    /** Get referenced row
    * @param string
    * @return NotORM_Row or null if the row does not exist
    */
    public function __get($name) {
        $tableName = $this->zTable->getTableSchema()->rawName;

        if (!($referencedColumn = $this->zTable->getReferencedColumn($name))) {
            throw new ZDbException('表 ' . $tableName . ' 没有与表 ' . Z::app()->getDb()->getTableRawName($name). ' 建立关系');
        }
        $column = $referencedColumn->name;

        $referenced = &$this->zTable->referencedTable[$name];

        if (!isset($referenced[$this[$column]])) { // referenced row may not exist
            return null;
        }

        return $referenced[$this[$column]];
    }
    
    /** Test if referenced row exists
    * @param string
    * @return bool
    */
    public function __isset($name) {
        return ($this->__get($name) !== null);
    }
    
    /** Store referenced value
    * @param string
    * @param NotORM_Row or null
    * @return null
    */
    public function __set($name, ZModel $value = null) {

        $column = $this->zTable->connection->structure->getReferencedColumn($name, $this->zTable->table);

        $this[$column] = $value;
    }
    
    /** Remove referenced column from data
    * @param string
    * @return null
    */
    public function __unset($name) {

        $column = $this->zTable->connection->structure->getReferencedColumn($name, $this->zTable->table);

        unset($this[$column]);
    }
    
    /** 
     * Update row
     * @return int number of affected rows or false in case of an error
    */
    public function save() {
        $zTable = $this->zTable;
        if ($this->isNew) {
            return $zTable->insert($this);
        } else {
            if (empty($this->_modified)) {
                return true;
            }

            return $zTable->where($zTable->primaryKey, $this[$zTable->primaryKey])->update($this);
        }
    }
    
    public function setProperty($property, $value)
    {
        if (!property_exists($this, $property)) {
           throw new ZDbException("xx");
        }

        $oldValue = $this->$property;
        if ($oldValue === $this->$property) {
            return $this;
        }
        $this->_modified[$property] = array($oldValue, $value);

        $this->$property = $value;
        $this->row[$property] = $value;
        return $this;
    }

    /** 
     * Delete row
     * @return int number of affected rows or false in case of an error
    */
    public function delete()
    {
        if ($this->isNew) {
            
        } else {
            $primaryKey = $this->zTable->primaryKey;
            return $zTable->where($primaryKey, $this->$primaryKey)->delete();
        }
    }
    
    protected function access($key, $delete = false) {
        if ($this->zTable->connection->cache && !isset($this->_modified[$key]) && $this->zTable->access($key, $delete)) {
            $id = (isset($this->row[$this->zTable->primaryKey]) ? $this->row[$this->zTable->primaryKey] : $this->row);
            $this->row = $this->zTable[$id]->row;
        }
    }
    
    // IteratorAggregate implementation
    
    public function getIterator() {
        $this->access(null);
        return new \ArrayIterator($this->row);
    }
    

    // Countable implementation
    
    public function count() {
        return count($this->row);
    }
    
    // ArrayAccess implementation
    
    /** Test if column exists
    * @param string column name
    * @return bool
    */
    public function offsetExists($key) {
        $this->access($key);
        $return = array_key_exists($key, $this->row);
        if (!$return) {
            $this->access($key, true);
        }
        return $return;
    }
    
    /** Get value of column
    * @param string column name
    * @return string
    */
    public function offsetGet($key) {
        $this->access($key);
        if (!array_key_exists($key, $this->row)) {
            $this->access($key, true);
        }
        return $this->row[$key];
    }
    
    /** Store value in column
    * @param string column name
    * @return null
    */
    public function offsetSet($key, $value) {
        $this->row[$key] = $value;
        $this->_modified[$key] = $value;
    }
    
    /** Remove column from data
    * @param string column name
    * @return null
    */
    public function offsetUnset($key) {
        unset($this->row[$key]);
        unset($this->_modified[$key]);
    }
    
}
