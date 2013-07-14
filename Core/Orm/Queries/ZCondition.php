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
    /**
     * where 条件 
     * @var  string
     */
    protected $whereString = '';

    static protected $isFirstWhere = true;

    protected $group = '';

    protected $order = '';

    protected $having;

    protected $offset;

    protected $limit = '';

    protected $tableSchema;

    protected $query;

    public function __construct(ZTableSchemaInterface $tableSchema,ZQuery $query)
    {
        $this->tableSchema = $tableSchema;
        $this->query = $query;
    }

    public function where($conditions, $operator = 'AND')
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
        return empty($this->whereString) ? '' : "\nWHERE" . $this->whereString . "\n" . $this->group
            . "\n" . $this->order;
    }


    public function orderBy($column, $direction = 'ASC')
    {
        $string = $this->query->buildColumnName($column) . ' ' . (strtoupper($direction) == 'ASC' ? 'ASC' : 'DESC');

        if (empty($this->order)) {
            $this->order = 'ORDER BY ' . $string;
        } else {
            $this->order =  $this->order . ',' . $string;
        }
        return $this;
    }

    /**
     * 添加分组条件
     * @param string
     * @return \Z\Core\Orm\Queries\ZCondition
     */
    public function groupBy($field)
    {
        $groups = [];
        if (strpos($field, ',') !== false) {
            $temp = explode(',', $field);
            foreach ($temp as $value) {
                $groups[] = $this->query->buildColumnName(trim($value));
            }
        } else {
            $groups[] = $this->query->buildColumnName(trim($field));
        }

        if (empty($this->group)) {
            $this->group = 'GROUP BY '. implode(',', $groups);
        } else {
            $this->group = $this->group . implode(',', $groups);
        }

        return $this;
    }

    /**
     * 设置查询范围
     * @param int $length
     * @param int $start
     * @return \Z\Core\Orm\Queries\ZCondition
     */
    public function limit ($length = NULL, $start = NULL)
    {
        $this->limit = func_num_args() ? array('start' =>(int)$start, 'length' =>(int)$length) : array();
        return $this;
    }
}