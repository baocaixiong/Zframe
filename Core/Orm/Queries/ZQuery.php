<?php
/**
 * ZQueryBuilder class
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
use Z\Core\Orm\ZTable;
use Z\Core\Orm\ZDbExpression;
use Z\Core\Orm\Exceptions\ZInvalidOperatorException;
use Z\Core\Orm\Exceptions\ZInaccurateColumnException;
use Z\Core\Orm\Exceptions\ZUnKnowMethodException;

class ZQuery extends ZObject
{
    /**
     * ZTable的实例
     * @var \Z\Core\Orm\ZTable
     */
    protected $table;

    /**
     * 本Table的Schema,为了方便
     * @var Z\Core\Orm\Schema\ZTableSchema
     */
    protected $tableSchema;


    const PARAM_PREFIX = ':ZPV';
    /**
     * 用来bandValue,实现预处理的参数
     * @var array
     */
    protected $parameters = array();

    protected static $paramCount = 0;

    protected $paramters = array();

    public function processConditions($conditions)
    {
        if (is_string($conditions)
            || (count($conditions) === 1 && strpos(($conditions = $conditions[0]), '=') !== false)
        ) {
            if (preg_match('~(\w+)\s*(!=|>=|<=|=|<>|<|>)\s*(\S*)~i', $conditions, $matches)) {

                $column = $this->buildColumnName($matches[1]);
                $operatorMin = $matches[2];
                $paramter = $this->createPrepareOrValue($matches[3]);
                return implode('', array($column, $operatorMin, $paramter));
            }

            return $conditions;
        } elseif (empty($conditions)) {
            return '';
        }

        $operator = trim(strtoupper(array_shift($conditions)));

        $count = count($conditions);

        if ($operator === 'AND' || $operator === 'OR') {
            
            $parts = array();
            for ($i = 0; $i < $count; $i++) {
                $condition = $this->processConditions($conditions[$i]);
                if ($condition !== '') {
                    $parts[] = '(' . $condition . ')';
                }
            }

            return empty($parts) ? '' : implode(' ' . $operator . ' ', $parts);
        }

        if ($count < 2) {
            return '';
        }

        $column = $conditions[0];
        $values = $conditions[1];
        if (!is_array($values)) {
            $values=array($values);
        }
            
        if ($operator === 'IN' || $operator === 'NOT IN') {
            if(empty($values)) {
                return $operator === 'IN' ? '0=1' : '';
            }
                
            foreach ($values as $i => $value) {
                if (is_string($value)) {
                    $values[$i] = $this->createPrepareOrValue($value);
                } else {
                    $values[$i] = (string)$value;
                }
            }
            return $this->buildColumnName($column) . ' '. $operator. ' (' . implode(', ', $values) . ')';
        }

        if ($operator === 'LIKE' || $operator === 'NOT LIKE' || $operator==='OR LIKE' || $operator === 'OR NOT LIKE') {
            if (empty($values)) {
                return $operator === 'LIKE' || $operator === 'OR LIKE' ? '0=1' : '';
            }
            if ($operator === 'LIKE' || $operator === 'NOT LIKE') {
                $andor = ' AND ';
            } else {
                $andor = ' OR ';
                $operator = $operator === 'OR LIKE' ? 'LIKE' : 'NOT LIKE';
            }

            $expressions = array();
            foreach ($values as $value) {
                $expressions[] = $this->buildColumnName($column) . ' ' . $operator. ' ' . $this->createPrepareOrValue($value);
            }
                
            return implode($andor, $expressions);
        }

        throw new ZInvalidOperatorException('Unknown operator "' . $operator . '".');
    }

    public function buildColumnName($columnName, $isVirtualColumn = false)
    {
        $isInSelfTable = !$isVirtualColumn && array_key_exists($columnName, $this->tableSchema->columns);
        
        if ($isInSelfTable) {
            $column = $this->tableSchema->columns[$columnName];

            return $this->tableSchema->rawName . '.' . $column->getRawName();
        } elseif (array_key_exists($columnName, $this->tableSchema->virtualColumns)) {
            $virtualColumn = $this->tableSchema->virtualColumns[$columnName];
            $rawTableName = $virtualColumn->getForeignKey()->getTableRawName();

            $this->join($virtualColumn->getForeignKey()->foreignName);
            return $rawTableName . '.' .  $virtualColumn->getRawName();
        }

        throw new ZInaccurateColumnException('错误的字段名称:' . $columnName);
    }

    public function createPrepareOrValue($value)
    {
        if (Z::app()->getDb()->emulatePrepare) {
            $return = self::PARAM_PREFIX . self::$paramCount;
            $this->parameters[self::PARAM_PREFIX . self::$paramCount++] = $value;
            return $return;
        } else {
            return $this->table->connection->quote($value);
        }
    }

    public function __call($func, $paramters)
    {
        $class = get_called_class();
        if (!method_exists($this->table, $func)) {
            throw new ZUnKnowMethodException('调用未知的方法 ' . $class . ':' . $func);
        }

        call_user_func_array(array($this->table, $func), $paramters);
    }

    public function getParamters()
    {
        return $this->paramters;
    }
}