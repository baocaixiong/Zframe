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

    protected function processConditions($conditions)
    {
        if (is_string($conditions)) {
            if (preg_match('~(\w+)\s*(!=|>=|<=|=|<>|<|>)\s*(\S+)~i', $conditions, $matches)) {
                $column = $this->buildColumnName($matches[1]);
                $operator = $matches[2];
                $paramter = $matches[3];
            }

            return $conditions;
        } elseif (empty($conditions)) {
            return '';
        }

        $operator = strtoupper(array_shift($conditions));
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
    }

    public function buildColumnName($columnName)
    {

        return $columnName;
    }
}