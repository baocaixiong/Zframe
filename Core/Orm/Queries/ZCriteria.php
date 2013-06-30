<?php
/**
 * ZCriteria class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Queries
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Queries;

use Z\Z;
use Z\Core\ZObject;
use Z\Core\Orm\ZOrmAbstract;

class ZCriteria extends ZOrmAbstract
{

    const PARAM_PREFIX = ':zcp';
    /**
     * 用来bandValue,实现预处理的参数
     * @var array
     */
    protected $parameters = array();

    protected static $paramCount = 0;

    /**
     * 已经处理好的条件
     * @var array
     */
    protected $condition = array();

    /**
     * where 条件 
     * @var array
     */
    protected $where = array();

    protected $group;

    protected $order;

    protected $having;

    protected $offset;

    public function __construct()
    {
        $this->connection = Z::app()->getDb();
        $this->driverName = $this->connection->getDriverName();
    }

    /**
     * 增加一个SQL条件
     * 
     * @param mixed $condition 
     * @param string $operator 
     * @return \Z\Core\Orm\Queries\ZCriteria
     */
    public function addCondition($condition, $operator = 'AND')
    {
        if (is_array($condition)) {
            if (empty($condition)) {
                return $this;
            }
            $condition = '(' . implode(') ' . $operator . ' (', $condition) . ')';
        }

        if (empty($this->condition)) {
            $this->condition = $condition;
        } else {
            $this->condition = '(' . $this->condition . ') ' . $operator . ' (' . $condition. ')';
        }

        return $this;
    }

    public function addSearchCondition($column, $keyword, $escape = true, $operator = 'AND', $like = 'LIKE')
    {
        if (empty($keyword)) {
            return $this;
        }
        if ($escape) {
            $keyword = '%' . strtr($keyword, array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')) . '%';
        }

        $condition = $column . " $like ". self::PARAM_PREFIX . self::$paramCount;
        $this->parameters[self::PARAM_PREFIX . self::$paramCount++] = $keyword;

        return $this->addWhere($condition, $operator);
    }

    public function addInCondition($column, $value, $operator = 'AND')
    {
        if (($n=count($values)) < 1)) {
            $condition = '0=1'; //from YII
        } elseif ($n === 1) {
            $value = reset($value);
            if (is_null($value)) {
                $condition = $column . ' IS NULL ';
            } else {
                $condition = $column . '=' . self::PARAM_PREFIX . self::$paramCount;
                $this->parameters[self::PARAM_PREFIX.self::$paramCount++]=$value;
            }
        } else {
            $parameters = array();
            foreach($values as $value)
            {
                $parameters[] = self::PARAM_PREFIX . self::$paramCount;
                $this->parameters[self::PARAM_PREFIX . self::$paramCount++] = $value;
            }
            $condition = $column . ' IN ('. implode(', ', $params) . ')';
        }

        return $this->addWhere($condition, $operator);
    }

    public function addNotInCondition($column, $value, $operator = 'AND')
    {
        if (($n=count($values)) < 1)) {
            $condition = '0=1'; //from YII
        } elseif ($n === 1) {
            $value = reset($value);
            if (is_null($value)) {
                $condition = $column . ' IS NOT NULL ';
            } else {
                $condition = $column . '=' . self::PARAM_PREFIX . self::$paramCount;
                $this->parameters[self::PARAM_PREFIX.self::$paramCount++]=$value;
            }
        } else {
            $parameters = array();
            foreach($values as $value)
            {
                $parameters[] = self::PARAM_PREFIX . self::$paramCount;
                $this->parameters[self::PARAM_PREFIX . self::$paramCount++] = $value;
            }
            $condition = $column . ' NOT IN ('. implode(', ', $params) . ')';
        }

        return $this->addWhere($condition, $operator);
    }


    public function addBetweenCondition($column, $valueStart, $valueEnd, $operator = 'AND')
    {
        if (empty($valueStart) || empty($valueEnd)) {
            return $this;
        }

        $paramStart = self::PARAM_PREFIX . self::$paramCount++;
        $paramEnd = self::PARAM_PREFIX . self::$paramCount++;

        $this->parameters[$paramStart] = $valueStart;
        $this->parameters[$paramEnd]=$valueEnd;
        $condition = "$column BETWEEN $paramStart AND $paramEnd";

        return $this->addCondition($condition,$operator);
    }

    public function addColumnCondition(array $columns, $columnOperator = 'AND', $operator = 'AND')
    {
        $parameters = array();

        foreach ($columns as $name => $value) {
            if (empty($value)) {
                $parameters [] = $name . ' IS NULL';
            } else {
                $parameters[] = $name . '=' . self::PARAM_PREFIX . self::$paramCount;
                $this->parameters[self::PARAM_PREFIX . self::$paramCount++] = $value;
            }
        }

        $this->addWhere(implode(" $columnOperator ", $params), $operator);
    }

    public function compare($column, $value, $partialMatch, $operator = 'AND', $escape = true)
    {
        if (is_array($value)) {
            if ($value===array()) {
                return $this;
            }
                
            return $this->addInCondition($column,$value,$operator);
        } else {
            $value = (string) $value;
        }

        if (preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/',$value,$matches)) {
            $value = $matches[2];
            $op = $matches[1];
        } else {
            $op = '';
        }

        if (empty($value)) {
            return $this;
        }

        if ($partialMatch) {
            if ($op === '') {
                return $this->addSearchCondition($column, $value, $escape, $operator);
            }
            if ($op === '<>') {
                return $this->addSearchCondition($column, $value, $escape, $operator, 'NOT LIKE');
            }
                
        } elseif( $op === '') {
            $op = '=';
        }
        
        $this->addCondition($column . $op . self::PARAM_PREFIX . self::$paramCount, $operator);
        $this->params[self::PARAM_PREFIX . self::$paramCount++] = $value;

        return $this;
    }
}