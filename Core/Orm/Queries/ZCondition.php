<?php
/**
 * ZCondition class
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
use ZTableSchemaInterface;

class ZCondition extends ZObject
{

    const PARAM_PREFIX = ':zcp';
    /**
     * 用来bandValue,实现预处理的参数
     * @var array
     */
    protected $parameters = array();

    protected static $paramCount = 0;

    /**
     * where 条件 
     * @var  string
     */
    protected $whereString = '';

    static protected $isFirstWhere = true;

    protected $group;

    protected $order;

    protected $having;

    protected $offset;

    protected $driverName;

    protected $tableSchema;

    protected $query;

    public function __construct(ZTableSchemaInterface $tableSchema,ZQuery $query)
    {
        $this->tableSchema = $tableSchema;
        $this->driverName = Z::app()->getDb()->getDriverName();
        $this->query = $query;
    }

    public function where($conditions, $operator = 'AND', $paramters = array())
    {
        $conditionString = $this->query->processConditions($conditions);
        if (self::$isFirstWhere) {
            self::$isFirstWhere = false;
            $operator = '';
        }

        if ($this->whereString === '') {
            $this->whereString = ' ' . $operator . ' (' . $conditionString . ') ';
        } else {
            $this->whereString = $this->whereString . ' ' . $operator . ' (' . $conditionString . ') ';
        }
        
    }

    public function __toString()
    {
        return $this->whereString;
    }

    /**
     * 增加一个SQL条件
     * 
     * @param mixed $condition 
     * @param string $operator 
     * @return \Z\Core\Orm\Queries\ZCriteria
     */
    protected function addCondition($condition, $operator = 'AND')
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

    /**
     * 添加一个Search的Codition
     * @param string  $column   [description]
     * @param string  $keyword  [description]
     * @param boolean $escape   [description]
     * @param string  $operator [description]
     * @param string  $like     [description]
     */
    protected function addSearchCondition($column, $keyword, $escape = true, $operator = 'AND', $like = 'LIKE')
    {
        if (empty($keyword)) {
            return $this;
        }
        if ($escape) {
            $keyword = '%' . strtr($keyword, array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')) . '%';
        }

        $condition = $column . " $like ". self::PARAM_PREFIX . self::$paramCount;
        $this->parameters[self::PARAM_PREFIX . self::$paramCount++] = $keyword;

        return $this->addCondition($condition, $operator);
    }

    /**
     * 添加一个In Where的Codition
     * @param string $column   [description]
     * @param string $value    [description]
     * @param string $operator [description]
     */
    protected function addInCondition($column, $value, $operator = 'AND')
    {
        if (($n = count($values)) < 1) {
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

        return $this->addCondition($condition, $operator);
    }

    /**
     * 添加一个Not Where的Condition
     * @param string $column   [description]
     * @param string $value    [description]
     * @param string $operator [description]
     */
    protected function addNotInCondition($column, $value, $operator = 'AND')
    {
        if (($n=count($values)) < 1) {
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

        return $this->addCondition($condition, $operator);
    }

    /**
     * 添加一个Between Where的Codition
     * @param string $column     [description]
     * @param string $valueStart [description]
     * @param string $valueEnd   [description]
     * @param string $operator   [description]
     */
    protected function addBetweenCondition($column, $valueStart, $valueEnd, $operator = 'AND')
    {
        if (empty($valueStart) || empty($valueEnd)) {
            return $this;
        }

        $paramStart = self::PARAM_PREFIX . self::$paramCount++;
        $paramEnd = self::PARAM_PREFIX . self::$paramCount++;

        $this->parameters[$paramStart] = $valueStart;
        $this->parameters[$paramEnd] = $valueEnd;
        $condition = "$column BETWEEN $paramStart AND $paramEnd";

        return $this->addCondition($condition,$operator);
    }

    protected function addColumnCondition(array $columns, $columnOperator = 'AND', $operator = 'AND')
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

        $this->addCondition(implode(" $columnOperator ", $params), $operator);
    }
}