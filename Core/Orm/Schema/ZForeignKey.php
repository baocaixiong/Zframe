<?php
/**
 * ZStructureConvention class
 * from NotORM
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Schema
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Schema;

use Z\Z;
use Z\Core\ZObject;
use Z\Core\Orm\ZTable;

class ZForeignKey extends ZObject
{
    protected $leftField;

    protected $currTable;

    protected $rightField;

    public function __construct($leftField, $currTable, $rightField = ZTable::PRIMARY_KEY)
    {
        $this->leftField = $leftField;
        $this->currTable = $currTable;
        $this->rightField = $rightField;
    }

    /**
     * current raw table name
     * @return string
     */
    public function getTableRawName()
    {
        return $this->currTable->getRawTableName();
    }

    /**
     * 获得左表字段
     * @return string
     */
    public function getLeftField()
    {
        return $this->leftField;
    }

    /**
     * 获得当前表对象
     * @return \Z\Core\Orm\ZTable
     */
    public function getTable()
    {
        return $this->currTable;
    }

    /**
     * 获得右表字段
     * @return string
     */
    public function getRightField()
    {
        return $this->rightField;
    }
}