<?php
/**
 * ZQueryBuilder class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Queries\Mysql
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Queries;

use Z\Core\Orm\Exceptions\ZAliasConflictException;
use Z\Core\Orm\Exceptions\ZUndefinedColumnException;
use Z\Core\Orm\Exceptions\ZJoinFieledException;
use ZTableInterface;

class ZSelect extends ZQuery
{
    /**
     * WHERE过滤条件准则
     * @var \Z\Core\Orm\Queries\ZCondition
     */
    protected $condition;

    /**
     * 要查询的字段
     * 作为一个索引数组。数组的键是alias，值是ColumnName
     * @var array
     */
    protected $fields = array();

    /**
     * SQL ORDER BY
     * @var string
     */
    protected $order = '';

    /**
     * SQL GROUP BY
     * @var string
     */
    protected $group = '';

    /**
     * HAVING 过滤条件
     * @var \Z\Core\Orm\Queries\ZCriteria
     */
    protected $having;

    /**
     * join table
     * @var \Z\Core\Orm\Queries\ZJoin
     */
    protected $join;

    protected $limit;

    /**
     * CONSTRUCT METHOD
     * 创建一个SelectQuery的实例
     * @param \Z\Core\Orm\ZTable $table  [description]
     * @param mixed              $fields [description]
     */
    public function __construct(ZTableInterface $table, $fields = array())
    {
        $this->table = $table;
        $this->tableSchema = $table->getTableSchema();
        $this->fields($fields);

        $this->condition = new ZCondition($this->tableSchema, $this);
    }

    /**
     * 选择要Select的字段
     * 1.不调用此方法或者此方法传递的参数为空，则表示要查询所有的字段
     * 2.fields('id', 'name', ..) 表示要查询的字段为id和name
     * 3.fields(array('id', 'name', ...)) 同上
     * 4.fields(array('id' => 'aliasId', 'name')),表示id AS aliasId, name AS name
     * 
     * @param  mixed  $fields 要查询的字段 
     * @return \Z\Core\Orm\Queries\ZSelect
     */
    public function fields($fields = array())
    {
        // var_dump($this->tableSchema->getForeignKey('book')->getTable()->getTableSchema()->getForeignKey('author')->getTable()->getTableSchema() === $this->tableSchema); //true
        $n = func_num_args();

        if ($n > 1) {
            $fields = func_get_args();
        }

        is_string($fields) && $fields= array($fields);

        foreach ($fields as $key => $value) {
            $columnName = is_numeric($key) ? $value : $key;

            if (!array_key_exists($columnName, $this->tableSchema->columns)) {
                throw new ZUndefinedColumnException('未知的列名 ' . $this->tableSchema->rawName . ':' . $columnName);
            }
            $column = $this->tableSchema->getColumn($columnName);
            if (!is_numeric($key)) {
                $column->alias = $value;
            }
            $aliasName = !is_numeric($key) ? $value : $column->alias;
            $this->_addField($column, $aliasName);
        }

        return $this;
    }


    public function where($conditions, $operator = 'AND')
    {
        $operator = strtoupper($operator);
        
        $this->condition->where($conditions, $operator);

        return $this;
    }

    public function andWhere($conditions)
    {
        $this->condition->where($conditions, 'AND');

        return $this;
    }

    public function orWhere($conditions)
    {
        $this->condition->where($conditions, 'OR');

        return $this;
    }

    public function join($foreignName, $relation = 'LEFT')
    {
        if (!array_key_exists($foreignName, $this->tableSchema->foreignKeys)) {
            throw new ZJoinFieledException('Join了一个未设置foreigKey的数据表: '. $foreignName);
        }

        $this->createJoinQuery();

        $this->join->createJoinString($foreignName, $relation);

        return $this;
    }

    public function rightJoin($foreignName)
    {
        return $this->join($foreignName, 'RIGHT');
    }

    protected function createJoinQuery()
    {
        $driverName = $this->table->driverName;

        if ($this->join === null) {
            $className = $this->table->getQueryNamespace() . '\\' . ucfirst($driverName) . '\\ZJoin';
            $this->join = new $className($this->tableSchema);
        }
    }

    /**
     * 添加Select字段和别名
     * @param $column
     * @param string $aliasName  alias name
     * @throws \Z\Core\Orm\Exceptions\ZAliasConflictException
     * @internal param \Z\Core\Orm\Schema\ZColumnSchema $columnName column name
     */
    public function _addField($column, $aliasName)
    {
        if (isset($this->fields[$aliasName])) {
            throw new ZAliasConflictException('重复的Column别名:' . $aliasName);
        }

        $this->fields[$aliasName] = $column;
    }


    public function __toString()
    {
        $queryString = 'SELECT ';

        $tableName = $this->tableSchema->rawName;
        if (empty($this->fields)) {
            $queryString .= $tableName . '.*';
        } else {
            $tempString = array();
            foreach ($this->fields as $alias => $column) {
                $tempString[] = $tableName . '.' . $column->rawName . ' AS `' . $alias . '`';   
            }
            $queryString .= implode(', ', $tempString);
        }
    
        $queryString .= "\nFROM";

        $queryString .= "\n$tableName";

        $queryString .= $this->join;

        $queryString .= "" . $this->condition;

        !empty($this->limit) and $queryString .= "\nLIMIT " . (int) $this->limit['length'] . ' OFFSET ' . (int) $this->limit['start'];
        return $queryString;
    }
    
    public function orderBy($column, $direction = 'ASC')
    {
        $this->condition->orderBy($column, $direction);

        return $this;
    }

    public function groupBy($field)
    {
        $this->condition->groupBy($field);

        return $this;
    }

    public function limit($length = null, $start = null)
    {
        $this->limit = func_num_args() ? array('start' =>(int)$start, 'length' =>(int)$length) : array();
        return $this;
    }

    public function having()
    {
        
    }

    /**
     * 查询出所有的记录
     *
     * @return mixed
     */
    public function fetchAll()
    {
        $reader = $this->table->fetchAll(2, $this->parameters);
        // var_dump($this->table->lastQuery);
        //var_dump($reader);
        return $reader;
    }
}
