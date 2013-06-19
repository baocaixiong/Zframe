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


class ZModel extends ZOrmAbstract implements IteratorAggregate, ArrayAccess, Countable {
    private $modified = array();

    protected $row, $zTable;
    
    /** @access protected must be public because it is called from zTable */
    public function __construct(array $row, ZTable $zTable) {
        $this->row = $row;
        $this->zTable = $zTable;
    }
    
    /** Get primary key value
    * @return string
    */
    public function __toString() {

        return (string) $this[$this->zTable->primary]; // (string) - PostgreSQL returns int

    }
    
    /** Get referenced row
    * @param string
    * @return NotORM_Row or null if the row does not exist
    */
    public function __get($name) {

        $column = $this->zTable->connection->structure->getReferencedColumn($name, $this->zTable->tableName);
        $referenced = &$this->zTable->referenced[$name];
        if (!isset($referenced)) {
            $keys = array();
            foreach ($this->zTable->rows as $row) {

                if ($row[$column] !== null) {
                    $keys[$row[$column]] = null;
                }
            }
            if ($keys) {

                $table = $this->zTable->connection->structure->getReferencedTable($name, $this->zTable->tableName);
                $referenced = new ZTable($table, $this->zTable->connection, $this);
                $referenced->where("$table." . $this->zTable->connection->structure->getPrimary($table), array_keys($keys));

            } else {
                $referenced = array();
            }
        }
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
    public function __set($name, NotORM_Row $value = null) {

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
    
    /** Get referencing rows
    * @param string table name
    * @param array (["condition"[, array("value")]])
    * @return NotORM_MultiResult
    */
    public function __call($name, array $args) {
        $table = $this->zTable->connection->structure->getReferencingTable($name, $this->zTable->table);
        $column = $this->zTable->connection->structure->getReferencingColumn($table, $this->zTable->table);
        $return = new NotORM_MultiResult($table, $this->zTable, $column, $this[$this->zTable->primary]);
        $return->where("$table.$column", array_keys((array) $this->zTable->rows)); // (array) - is null after insert

        if ($args) {
            call_user_func_array(array($return, 'where'), $args);
        }
        return $return;
    }
    
    /** Update row
    * @param array or null for all modified values
    * @return int number of affected rows or false in case of an error
    */
    public function update($data = null) {
        // update is an SQL keyword
        if (!isset($data)) {
            $data = $this->modified;
        }
        $zTable = new ZTable($this->zTable->tableName, $this->zTable->connection);
        return $zTable->where($this->zTable->primaryKey, $this[$this->zTable->primaryKey])->update($data);
    }
    
    /** Delete row
    * @return int number of affected rows or false in case of an error
    */
    public function delete() {
        // delete is an SQL keyword
        $zTable = new ZTable($this->zTable->tableName, $this->zTable->connection);
        return $zTable->where($this->zTable->primaryKey, $this[$this->zTable->primaryKey])->delete();
    }
    
    protected function access($key, $delete = false) {

        if ($this->zTable->connection->cache && !isset($this->modified[$key]) && $this->zTable->access($key, $delete)) {
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
        $this->modified[$key] = $value;
    }
    
    /** Remove column from data
    * @param string column name
    * @return null
    */
    public function offsetUnset($key) {
        unset($this->row[$key]);
        unset($this->modified[$key]);
    }
    
}
