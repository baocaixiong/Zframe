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

    public function processConditions($conditions)
    {
        if (is_string($conditions)) {
            if (preg_match('~(\w+)\s*(!=|>=|<=|=|<>|<|>)\s*(\S+)~i', $conditions, $matches)) {
                $column = $this->buildColumnName($matches[1]);
                $operatorMin = $matches[2];
                $paramter = $matches[3];
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
                    $values[$i] = $this->quoteValue($value);
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
                $expressions[] = $this->buildColumnName($column) . ' ' . $operator. ' ' . $this->quoteValue($value);
            }
                
            return implode($andor, $expressions);
        }

        throw new ZInvalidOperatorException('Unknown operator "' . $operator . '".');
    }

    public function buildColumnName($columnName, $isVirtualColumn = false)
    {
        $isInSelfTable = !$isVirtualColumn && array_key_exists($columnName, $this->tableSchema->columns);
        if ($isInSelfTable) {
            return $this->tableSchema->rawName . '.' . $this->tableSchema->columns[$columnName]->getRawName();
        } elseif (array_key_exists($columnName, $this->tableSchema->virtualColumns)) {
            $virtualColumn = $this->tableSchema->virtualColumns[$columnName];
            $rawTableName = $virtualColumn->getForeignKey()->getTableRawName();

            $this->createJoin($virtualColumn->getForeignKey()->foreignName);
            return $rawTableName . '.' .  $virtualColumn->getRawName();
        }

        throw new ZInaccurateColumnException('错误的字段名称:' . $columnName);
    }


    public function createJoin($foreignName, $relation = 'LEFT')
    {

    }

    public function quote($val)
    {
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

        return $this->table->connection->pdo->quote($val);
    }

    protected function removeExtraDots($expression) {
        return preg_replace('@(?:\\b[a-z_][a-z0-9_.:]*[.:])?([a-z_][a-z0-9_]*)[.:]([a-z_*])@i', '\\1.\\2', $expression); // rewrite tab1.tab2.col
    }

    protected function formatValue($val) {
        if ($val instanceof \DateTime) {
            return $val->format("Y-m-d H:i:s"); //! may be driver specific
        }
        return $val;
    }

    public function quoteValue($str)
    {
        if (is_int($str) || is_float($str)) {
            return $str;
        }

        if (($value=$this->table->connection->pdo->quote($str)) !== false) {
            return $value;
        } else {
            return "'" . addcslashes(str_replace("'", "''", $str), "\000\n\r\\\032") . "'";
        }
    }
}