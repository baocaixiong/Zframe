<?php
/**
 * ZVirtualColumn class
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


class ZVirtualColumn extends ZObject
{
    /**
     * foreign key instance 
     * @var \Z\Core\Orm\Schema\ZForeignKey
     */
    protected $foreignKey;

    /**
     * 字段名称
     * @var string
     */
    protected $columnName;

    protected $selfTableSchema;

    protected $rawName;

    /**
     * CONSTRUCT METHOD
     * @param \Z\Core\Orm\Schema\ZForeignKey $foreignKey foreign key instance 
     * @param string                         $fieldName  fieldName
     */
    public function __construct(ZForeignKey $foreignKey, $tableSchema, $fieldName)
    {
        $this->foreignKey = $foreignKey;
        $this->selfTableSchema = $tableSchema;
        $this->columnName = $fieldName;
    }

    /**
     * get foreign key instance
     * @return \Z\Core\Orm\Schema\ZForeignKey
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * virtual field name
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    public function getRawName() {
        if ($this->rawName === null) {
            $this->rawName = $this->selfTableSchema->getColumn($this->columnName)->getRawName();
        }

        return $this->rawName;
    }

    public function __toString()
    {
        return $this->getRawName;
    }
}