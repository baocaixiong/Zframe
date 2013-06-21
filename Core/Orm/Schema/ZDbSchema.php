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

abstract class ZDbSchema extends ZObject
{
    /**
     * 
     * @var array
     */
    public $columnTypes = array();


    /**
     * tables name
     * @var array like array('tableName1', ...)
     */
    private $_tableNames;

    /**
     * table instances
     * every one is instance of ZTableSchema
     * @var array like array('tableName1' => ZDbTableScheme Instance, ...)
     */
    private $_tableInstances;

    /**
     * database connection
     * contain PDO instance, driveName,tablePrefix
     * database account and so on
     * @var Z\Core\Orm\ZDbConnection
     */
    private $_connection;
    
    /**
     * CONSTRUCT METHOD
     *  
     * @param  Z\Core\Orm\ZDbConnection $connection database connection
     * @return \Z\Core\Orm\Schema\ZDbSchema
     */
    public function __construct(ZDbConnection $connection)
    {
        $this->_connection = $connection;
    }

    public getTableInstance()
    {
        
    }
}