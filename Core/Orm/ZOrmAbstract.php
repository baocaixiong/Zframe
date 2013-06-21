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
use Z\Core\ZCore;

abstract class ZOrmAbstract extends ZCore
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
     * 缓存
     * @var \Z\Caching\ZCacheAbstract
     */
    protected $cache;

    /**
     * table name
     * 如果有tablePrefix的话，此name为不含tablePrefix的table名
     * @var string
     */
    protected $tableName;

    
}