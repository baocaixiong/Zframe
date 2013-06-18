<?php
/**
 * ZTable class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm;

use Z\Z;
use Z\Core\ZObject;

abstract class ZOrmAbstract extends ZObject
{
    /**
     * 数据库连接
     * @var \Z\Core\Orm\ZDbConnection
     */
    protected $connection;

    /**
     * 数据库驱动名称
     * @var string
     */
    protected $driverName;

    /**
     * 相当于数据库 schema
     * @var \Z\Core\Orm\Strcutures\ZStructureInterface
     */
    protected $structure;

    /**
     * 缓存
     * @var \Z\Caching\ZCacheAbstract
     */
    protected $cache;

    /**
     * table name
     * @var string
     */
    protected $tableName;

    /**
     * 表主键
     * @var string primary key
     */
    protected $primaryKey;

    protected $rows;

    protected $referenced;
}