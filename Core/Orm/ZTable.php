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



class ZTable extends ZOrmAbstract implements \Iterator, \ArrayAccess, \Countable
{
    protected $single;

    protected $select = array();
    protected $conditions = array();
    protected $where = array();
    protected $parameters = array();
    protected $order = array();
    protected $limit = null;
    protected $offset = null;
    protected $group = "";
    protected $having = "";
    protected $lock = null;

    protected $union = array();
    protected $unionOrder = array();
    protected $unionLimit = null;
    protected $unionOffset = null;
    
    protected $data;
    protected $referencing = array();
    protected $aggregation = array();
    protected $accessed;
    protected $access;
    protected $keys = array();
    
    protected $modelClass;

    /**
    * Create table result
    * 
    * @param  string                    $tableName table name
    * @param  \Z\Core\Orm\ZDbConnection $db        db connection
    * @param  boolean                   $single    
    * 
    * @return \Z\Core\Orm\ZTable
    */

    public function __construct($tableName, ZDbConnection $db,$modelClass, $single = false) {
        $this->tableName = $tableName;
        $this->connection = $db;
        $this->single = $single;
        $this->modelClass = $modelClass;
        $this->primaryKey = $db->structure->getPrimary($tableName);
    }
    
    /** 
    * Add select clause, more calls appends to the end
    * 
    * @param string for example "column, MD5(column) AS column_md5"
    * @param string ...
    * @return NotORM_Result fluent interface
    */
    public function select($columns) {
        $this->__destruct();
        foreach (func_get_args() as $columns) {
            $this->select[] = $columns;
        }
        return $this;
    }

    /** Save data to cache and empty result
    */
    public function __destruct() {
        if ($this->connection->cache && !$this->select && isset($this->rows)) {
            $access = $this->access;
            if (is_array($access)) {
                $access = array_filter($access);
            }
            $this->connection->cache->set("$this->tableName;" . implode(",", $this->conditions), $access);
        }
        $this->rows = null;
        $this->data = null;
    }
    
    /**
     * 创建limit statement
     * @param  string $limit  limit like (0, 10)
     * @param  string $offset offset statement
     * @return string
     */
    protected function limitString($limit, $offset) {
        $return = "";
        if (isset($limit)) {
            $return .= " LIMIT $limit";
            if (isset($offset)) {
                $return .= " OFFSET $offset";
            }
        }
        return $return;
    }
    
    protected function removeExtraDots($expression) {
        return preg_replace('~(?:\\b[a-z_][a-z0-9_.:]*[.:])?([a-z_][a-z0-9_]*)[.:]([a-z_*])~i', '\\1.\\2', $expression); // rewrite tab1.tab2.col
    }
    
    protected function whereString() {
        $return = "";
        if ($this->group) {
            $return .= " GROUP BY $this->group";
        }
        if ($this->having) {
            $return .= " HAVING $this->having";
        }
        if ($this->order) {
            $return .= " ORDER BY " . implode(", ", $this->order);
        }
        $return = $this->removeExtraDots($return);
        
        $where = $this->where;
        if (isset($this->limit) && $this->connection->driverName == "oci") {
            $where[] = ($this->offset ? "rownum > $this->offset AND " : "") . "rownum <= " . ($this->limit + $this->offset); //! rownum > doesn't work - requires subselect (see adminer/drivers/oracle.inc.php)
        }
        if ($where) {
            $return = " WHERE (" . implode(") AND (", $where) . ")$return";
        }
        
        if ($this->connection->driverName != "oci" && $this->connection->driverName != "dblib") {
            $return .= $this->limitString($this->limit, $this->offset);
        }
        if (isset($this->lock)) {
            $return .= ($this->lock ? " FOR UPDATE" : " LOCK IN SHARE MODE");
        }
        return $return;
    }
    
    protected function topString() {
        if (isset($this->limit) && $this->connection->driverName == "dblib") {
            return " TOP ($this->limit)"; //! offset is not supported
        }
        return "";
    }
    
    protected function createJoins($val) {
        $return = array();
        preg_match_all('~\\b([a-z_][a-z0-9_.:]*[.:])[a-z_*]~i', $val, $matches);
        foreach ($matches[1] as $names) {
            $parent = $this->tableName;
            if ($names != "$parent.") { // case-sensitive
                preg_match_all('~\\b([a-z_][a-z0-9_]*)([.:])~i', $names, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    list(, $name, $delimiter) = $match;
                    $table = $this->connection->structure->getReferencedTable($name, $parent);
                    $column = ($delimiter == ':' ? $this->connection->structure->getPrimary($parent) : $this->connection->structure->getReferencedColumn($name, $parent));
                    $primary = ($delimiter == ':' ? $this->connection->structure->getReferencedColumn($parent, $table) : $this->connection->structure->getPrimary($table));
                    $return[$name] = " LEFT JOIN $table" . ($table != $name ? " AS $name" : "") . " ON $parent.$column = $name.$primary"; // should use alias if the table is used on more places
                    $parent = $name;
                }
            }
        }
        return $return;
    }
    
    /** Get SQL query
    * @return string
    */
    public function __toString() {
        $return = "SELECT" . $this->topString() . " ";
        $join = $this->createJoins(implode(",", $this->conditions) . "," . implode(",", $this->select) . ",$this->group,$this->having," . implode(",", $this->order));
        if (!isset($this->rows) && !empty($this->connection->cache) && !is_string($this->accessed)) {
            $this->accessed = $this->connection->cache->load("$this->table;" . implode(",", $this->conditions));
            $this->access = $this->accessed;
        }

        if ($this->select) {
            $return .= $this->removeExtraDots(implode(", ", $this->select));
        } elseif ($this->accessed) {
            $return .= ($join ? "$this->table." : "") . implode(", " . ($join ? "$this->table." : ""), array_keys($this->accessed));
        } else {
            $return .= ($join ? "$this->table." : "") . "*";
        }

        $return .= " FROM $this->tableName" . implode($join) . $this->whereString();
        if ($this->union) {
            $return = ($this->connection->driverName == "sqlite" || $this->connection->driverName == "oci" ? $return : "($return)") . implode($this->union);

            if ($this->unionOrder) {
                $return .= " ORDER BY " . implode(", ", $this->unionOrder);
            }
            $return .= $this->limitString($this->unionLimit, $this->unionOffset);
        }
        return $return;
    }
    
    protected function query($query, $parameters) {
        if ($this->connection->debug) {
            if (!is_callable($this->connection->debug)) {
                $debug = "$query;";
                if ($parameters) {
                    $debug .= " -- " . implode(", ", array_map(array($this, 'quote'), $parameters));
                }
                $pattern = '(^' . preg_quote(dirname(__FILE__)) . '(\\.php$|[/\\\\]))'; // can be static
                foreach (debug_backtrace() as $backtrace) {
                    if (isset($backtrace["file"]) && !preg_match($pattern, $backtrace["file"])) { // stop on first file outside NotORM source codes
                        break;
                    }
                }
                fwrite(STDERR, "$backtrace[file]:$backtrace[line]:$debug\n");
            } elseif (call_user_func($this->connection->debug, $query, $parameters) === false) {
                return false;
            }
        }
        $return = $this->connection->pdo->prepare($query);
        if (!$return || !$return->execute(array_map(array($this, 'formatValue'), $parameters))) {
            return false;
        }
        return $return;
    }
    
    protected function formatValue($val) {
        if ($val instanceof DateTime) {
            return $val->format("Y-m-d H:i:s"); //! may be driver specific
        }
        return $val;
    }
    
    protected function quote($val) {
        if (!isset($val)) {
            return "NULL";
        }
        if (is_array($val)) { // (a, b) IN ((1, 2), (3, 4))
            return "(" . implode(", ", array_map(array($this, 'quote'), $val)) . ")";
        }
        $val = $this->formatValue($val);
        if (is_float($val)) {
            return sprintf("%F", $val); // otherwise depends on setlocale()
        }
        if ($val === false) {
            return "0";
        }
        if (is_int($val) || $val instanceof ZDbExpression) { // number or SQL code - for example "NOW()"
            return (string) $val;
        }
        return $this->connection->pdo->quote($val);
    }
    
    /** Insert row in a table
    * @param mixed array($column => $value)|Traversable for single row insert or NotORM_Result|string for INSERT ... SELECT
    * @param ... used for extended insert
    * @return NotORM_Row inserted row or false in case of an error or number of affected rows for INSERT ... SELECT
    */
    public function insert($data) {
        if ($this->connection->freeze) {
            return false;
        }
        $parameters = array();
        if ($data instanceof NotORM_Result) {
            $parameters = $data->parameters; //! other parameters
            $data = (string) $data;
        } elseif ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }
        $insert = $data;
        if (is_array($data)) {
            $values = array();
            foreach (func_get_args() as $value) {
                if ($value instanceof Traversable) {
                    $value = iterator_to_array($value);
                }
                $values[] = $this->quote($value);
                foreach ($value as $val) {
                    if ($val instanceof ZDbExpression && $val->parameters) {
                        $parameters = array_merge($parameters, $val->parameters);
                    }
                }
            }
            //! driver specific empty $data and extended insert
            $insert = "(" . implode(", ", array_keys($data)) . ") VALUES " . implode(", ", $values);
        }
        // requires empty $this->parameters
        $return = $this->query("INSERT INTO $this->table $insert", $parameters);
        if (!$return) {
            return false;
        }
        $this->rows = null;
        if (!is_array($data)) {
            return $return->rowCount();
        }
        if (!isset($data[$this->primary]) && ($id = $this->connection->pdo->lastInsertId($this->connection->structure->getSequence($this->table)))) {
            $data[$this->primary] = $id;
        }
        return new $this->modelClass($data, $this);

    }
    
    /** Update all rows in result set
    * @param array ($column => $value)
    * @return int number of affected rows or false in case of an error
    */
    public function update(array $data) {
        if ($this->connection->freeze) {
            return false;
        }
        if (!$data) {
            return 0;
        }
        $values = array();
        $parameters = array();
        foreach ($data as $key => $val) {
            // doesn't use binding because $this->parameters can be filled by ? or :name
            $values[] = "$key = " . $this->quote($val);
            if ($val instanceof ZDbExpression && $val->parameters) {
                $parameters = array_merge($parameters, $val->parameters);
            }
        }
        if ($this->parameters) {
            $parameters = array_merge($parameters, $this->parameters);
        }
        // joins in UPDATE are supported only in MySQL
        $return = $this->query("UPDATE" . $this->topString() . " $this->table SET " . implode(", ", $values) . $this->whereString(), $parameters);
        if (!$return) {
            return false;
        }
        return $return->rowCount();
    }
    
    /** Insert row or update if it already exists
    * @param array ($column => $value)
    * @param array ($column => $value)
    * @param array ($column => $value), empty array means use $insert
    * @return int number of affected rows or false in case of an error
    */
    public function insert_update(array $unique, array $insert, array $update = array()) {
        if (!$update) {
            $update = $insert;
        }
        $insert = $unique + $insert;
        $values = "(" . implode(", ", array_keys($insert)) . ") VALUES " . $this->quote($insert);
        //! parameters
        if ($this->connection->driverName == "mysql") {
            $set = array();
            if (!$update) {
                $update = $unique;
            }
            foreach ($update as $key => $val) {
                $set[] = "$key = " . $this->quote($val);
                //! parameters
            }
            return $this->insert("$values ON DUPLICATE KEY UPDATE " . implode(", ", $set));
        } else {
            $connection = $this->connection->pdo;
            $errorMode = $connection->getAttribute(PDO::ATTR_ERRMODE);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            try {
                $return = $this->insert($values);
                $connection->setAttribute(PDO::ATTR_ERRMODE, $errorMode);
                return $return;
            } catch (PDOException $e) {
                $connection->setAttribute(PDO::ATTR_ERRMODE, $errorMode);
                if ($e->getCode() == "23000") { // "23000" - duplicate key
                    if (!$update) {
                        return 0;
                    }
                    $clone = clone $this;
                    $return = $clone->where($unique)->update($update);
                    return ($return ? $return + 1 : $return);
                }
                if ($errorMode == PDO::ERRMODE_EXCEPTION) {
                    throw $e;
                } elseif ($errorMode == PDO::ERRMODE_WARNING) {
                    trigger_error("PDOStatement::execute(): " . $e->getMessage(), E_USER_WARNING); // E_WARNING is unusable
                }
            }
        }
    }
    
    /** Delete all rows in result set
    * @return int number of affected rows or false in case of an error
    */
    public function delete() {
        if ($this->connection->freeze) {
            return false;
        }
        $return = $this->query("DELETE" . $this->topString() . " FROM $this->table" . $this->whereString(), $this->parameters);
        if (!$return) {
            return false;
        }
        return $return->rowCount();
    }
    
    /** Add where condition, more calls appends with AND
    * @param string condition possibly containing ? or :name
    * @param mixed array accepted by PDOStatement::execute or a scalar value
    * @param mixed ...
    * @return NotORM_Result fluent interface
    */
    public function where($condition, $parameters = array()) {
        if (is_array($condition)) { // where(array("column1" => 1, "column2 > ?" => 2))
            foreach ($condition as $key => $val) {
                $this->where($key, $val);
            }
            return $this;
        }
        $this->__destruct();
        $this->conditions[] = $condition;
        $condition = $this->removeExtraDots($condition);
        $args = func_num_args();
        if ($args != 2 || strpbrk($condition, "?:")) { // where("column < ? OR column > ?", array(1, 2))
            if ($args != 2 || !is_array($parameters)) { // where("column < ? OR column > ?", 1, 2)
                $parameters = func_get_args();
                array_shift($parameters);
            }
            $this->parameters = array_merge($this->parameters, $parameters);
        } elseif (is_null($parameters)) { // where("column", null)
            $condition .= " IS NULL";
        } elseif ($parameters instanceof NotORM_Result) { // where("column", $db->$table())
            $clone = clone $parameters;
            if (!$clone->select) {
                $clone->select($this->connection->structure->getPrimary($clone->table));
            }
            if ($this->connection->driverName != "mysql") {
                if ($clone instanceof NotORM_MultiResult) {
                    array_shift($clone->select);
                    $clone->single();
                }
                $condition .= " IN ($clone)";
                $this->parameters = array_merge($this->parameters, $clone->parameters);
            } else {
                $in = array();
                foreach ($clone as $row) {
                    $row = array_values(iterator_to_array($row));
                    if ($clone instanceof NotORM_MultiResult && count($row) > 1) {
                        array_shift($row);
                    }
                    if (count($row) == 1) {
                        $in[] = $this->quote($row[0]);
                    } else {
                        $in[] = $this->quote($row);
                    }
                }
                if ($in) {
                    $condition .= " IN (" . implode(", ", $in) . ")";
                } else {
                    $condition = "($condition) IS NOT NULL AND $condition IS NULL"; // $condition = "NOT id"
                }
            }
        } elseif (!is_array($parameters)) { // where("column", "x")
            $condition .= " = " . $this->quote($parameters);
        } else { // where("column", array(1, 2))
            if (!$parameters) {
                $condition = "($condition) IS NOT NULL AND $condition IS NULL";
            } elseif ($this->connection->driverName != "oci") {
                $condition .= " IN " . $this->quote($parameters);
            } else { // http://download.oracle.com/docs/cd/B19306_01/server.102/b14200/expressions014.htm
                $or = array();
                for ($i=0; $i < count($parameters); $i += 1000) {
                    $or[] = "$condition IN " . $this->quote(array_slice($parameters, $i, 1000));
                }
                $condition = "(" . implode(" OR ", $or) . ")";
            }
        }
        $this->where[] = $condition;
        return $this;
    }
    
    /** Shortcut for where()
    * @param string
    * @param mixed
    * @param mixed ...
    * @return NotORM_Result fluent interface
    */
    public function __invoke($where, $parameters = array()) {
        $args = func_get_args();
        return call_user_func_array(array($this, 'where'), $args);
    }
    
    /** Add order clause, more calls appends to the end
    * @param string for example "column1, column2 DESC"
    * @param string ...
    * @return NotORM_Result fluent interface
    */
    public function order($columns) {
        $this->rows = null;
        foreach (func_get_args() as $columns) {
            if ($this->union) {
                $this->unionOrder[] = $columns;
            } else {
                $this->order[] = $columns;
            }
        }
        return $this;
    }
    
    /** Set limit clause, more calls rewrite old values
    * @param int
    * @param int
    * @return NotORM_Result fluent interface
    */
    public function limit($limit, $offset = null) {
        $this->rows = null;
        if ($this->union) {
            $this->unionLimit = +$limit;
            $this->unionOffset = +$offset;
        } else {
            $this->limit = +$limit;
            $this->offset = +$offset;
        }
        return $this;
    }
    
    /** Set group clause, more calls rewrite old values
    * @param string
    * @param string
    * @return NotORM_Result fluent interface
    */
    public function group($columns, $having = "") {
        $this->__destruct();
        $this->group = $columns;
        $this->having = $having;
        return $this;
    }
    
    /** Set select FOR UPDATE or LOCK IN SHARE MODE
    * @param bool
    * @return NotORM_Result fluent interface
    */
    public function lock($exclusive = true) {
        $this->lock = $exclusive;
        return $this;
    }
    
    /** 
    * @param NotORM_Result
    * @param bool
    * @return NotORM_Result fluent interface
    */
    public function union(NotORM_Result $result, $all = false) {
        $this->union[] = " UNION " . ($all ? "ALL " : "") . ($this->connection->driverName == "sqlite" || $this->connection->driverName == "oci" ? $result : "($result)");
        $this->parameters = array_merge($this->parameters, $result->parameters);
        return $this;
    }
    
    /** Execute aggregation function
    * @param string
    * @return string
    */
    public function aggregation($function) {
        $join = $this->createJoins(implode(",", $this->conditions) . ",$function");
        $query = "SELECT $function FROM $this->table" . implode($join);
        if ($this->where) {
            $query .= " WHERE (" . implode(") AND (", $this->where) . ")";
        }
        foreach ($this->query($query, $this->parameters)->fetch() as $return) {
            return $return;
        }
    }
    
    /** Count number of rows
    * @param string
    * @return int
    */
    public function count($column = "") {
        if (!$column) {
            $this->execute();
            return count($this->data);
        }
        return $this->aggregation("COUNT($column)");
    }
    
    /** Return minimum value from a column
    * @param string
    * @return int
    */
    public function min($column) {
        return $this->aggregation("MIN($column)");
    }
    
    /** Return maximum value from a column
    * @param string
    * @return int
    */
    public function max($column) {
        return $this->aggregation("MAX($column)");
    }
    
    /** Return sum of values in a column
    * @param string
    * @return int
    */
    public function sum($column) {
        return $this->aggregation("SUM($column)");
    }
    
    /** Execute the built query
    * @return null
    */
    protected function execute() {
        if (!isset($this->rows)) {
            $result = false;
            $exception = null;
            $parameters = array();
            foreach (array_merge($this->select, array($this, $this->group, $this->having), $this->order, $this->unionOrder) as $val) {
                if (($val instanceof ZDbExpression || $val instanceof self) && $val->parameters) {
                    $parameters = array_merge($parameters, $val->parameters);
                }
            }
            try {
                $result = $this->query($this->__toString(), $parameters);
            } catch (\PDOException $exception) {
                // handled later
            }
            if (!$result) {
                if (!$this->select && $this->accessed) {
                    $this->accessed = '';
                    $this->access = array();
                    $result = $this->query($this->__toString(), $parameters);
                } elseif ($exception) {
                    throw $exception;
                }
            }
            $this->rows = array();
            if ($result) {
                $result->setFetchMode(\PDO::FETCH_ASSOC);
                foreach ($result as $key => $row) {
                    if (isset($row[$this->primaryKey])) {
                        $key = $row[$this->primaryKey];
                        if (!is_string($this->access)) {
                            $this->access[$this->primaryKey] = true;
                        }
                    }
                    $className = $this->modelClass;

                    $this->rows[$key] = new $className($row, $this);
                }
            }
            $this->data = $this->rows;
        }
    }
    
    /** Fetch next row of result
    * @return NotORM_Row or false if there is no row
    */
    public function fetch() {
        $this->execute();
        $return = current($this->data);
        next($this->data);
        return $return;
    }
    
    /** Fetch all rows as associative array
    * @param string
    * @param string column name used for an array value or an empty string for the whole row
    * @return array
    */
    public function fetchPairs($key, $value = '') {
        $return = array();
        $clone = clone $this;
        if ($value != "") {
            $clone->select = array();
            $clone->select("$key, $value"); // MultiResult adds its column
        } elseif ($clone->select) {
            array_unshift($clone->select, $key);
        } else {
            $clone->select = array("$key, $this->table.*");
        }
        foreach ($clone as $row) {
            $values = array_values(iterator_to_array($row));
            $return[$values[0]] = ($value != "" ? $values[(isset($values[1]) ? 1 : 0)] : $row); // isset($values[1]) - fetchPairs("id", "id")
        }
        return $return;
    }
    
    protected function access($key, $delete = false) {
        if ($delete) {
            if (is_array($this->access)) {
                $this->access[$key] = false;
            }
            return false;
        }
        if (!isset($key)) {
            $this->access = '';
        } elseif (!is_string($this->access)) {
            $this->access[$key] = true;
        }
        if (!$this->select && $this->accessed && (!isset($key) || !isset($this->accessed[$key]))) {
            $this->accessed = '';
            $this->rows = null;
            return true;
        }
        return false;
    }
    
    protected function single() {
    }
    
    // Iterator implementation (not IteratorAggregate because $this->data can be changed during iteration)
    
    public function rewind() {
        $this->execute();
        $this->keys = array_keys($this->data);
        reset($this->keys);
    }
    
    /** @return NotORM_Row */
    public function current() {
        return $this->data[current($this->keys)];
    }
    
    /** @return string row ID */
    public function key() {
        return current($this->keys);
    }
    
    public function next() {
        next($this->keys);
    }
    
    public function valid() {
        return current($this->keys) !== false;
    }
    
    // ArrayAccess implementation
    
    /** Test if row exists
    * @param string row ID or array for where conditions
    * @return bool
    */
    public function offsetExists($key) {
        $row = $this->offsetGet($key);
        return isset($row);
    }
    
    /** Get specified row
    * @param string row ID or array for where conditions
    * @return NotORM_Row or null if there is no such row
    */
    public function offsetGet($key) {
        if ($this->single && !isset($this->data)) {
            $clone = clone $this;
            if (is_array($key)) {
                $clone->where($key)->limit(1);
            } else {
                $clone->where($this->primary, $key);
            }
            $return = $clone->fetch();
            if (!$return) {
                return null;
            }
            return $return;
        } else {
            $this->execute();
            if (is_array($key)) {
                foreach ($this->data as $row) {
                    foreach ($key as $k => $v) {
                        if ((isset($v) && $row[$k] !== null ? $row[$k] != $v : $row[$k] !== $v)) {
                            break;
                        }
                        return $row;
                    }
                }
            } elseif (isset($this->data[$key])) {
                return $this->data[$key];
            }
        }
    }
    
    /** Mimic row
    * @param string row ID
    * @param NotORM_Row
    * @return null
    */
    public function offsetSet($key, $value) {
        $this->execute();
        $this->data[$key] = $value;
    }
    
    /** Remove row from result set
    * @param string row ID
    * @return null
    */
    public function offsetUnset($key) {
        $this->execute();
        unset($this->data[$key]);
    }
    
}