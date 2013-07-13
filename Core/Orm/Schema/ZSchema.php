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

use Z\Core\ZObject;
use Z\Core\Orm\ZDbConnection;

abstract class ZSchema extends ZObject
{
    private $_rawName;

    public function getRawName()
    {
        return $this->_rawName;
    }

    public function setRawName($value)
    {
        $this->_rawName = $value;
    }

    public function __toString()
    {
        return $this->_rawName();
    }
}