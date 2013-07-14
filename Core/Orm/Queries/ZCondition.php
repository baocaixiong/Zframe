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

    protected $group;

    protected $order;

    protected $having;

    protected $offset;

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
        return empty($this->whereString) ? '' : "\nWHERE" . $this->whereString;
    }
}