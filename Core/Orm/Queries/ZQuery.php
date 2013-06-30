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
use Z\Core\Orm\ZOrmAbstract;
use Z\Core\Orm\ZTable;

class ZQuery extends ZOrmAbstract
{
    /**
     * 要查询的字段
     * @param  string $columns 要查询的字段，可以是多个
     * @return \Z\Core\Orm\ZTable
     */
    public function select($columns)
    {
        $this->__destruct();
        foreach (func_get_args() as $columns) {
            $this->select[] = $columns;
        }
        return $this;
    }
}