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
    private $_tables;

    /**
     * database connection
     * contain PDO instance, driveName,tablePrefix
     * database account and so on
     * @var Z\Core\Orm\ZDbConnection
     */
    private $_connection;

    /**
     * 获得链接
     * @param  \Z\Core\Orm\ZDbConnection $con db connection
     * @return \Z\Core\Orm\Schema\ZSchema
     */
    public function __construct(ZDbConnection $con)
    {
        $this->_connection = $con;
    }

    /**
     * 获得链接
     * @return \Z\Core\Orm\ZDbConnection
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    abstract function loadTable();


    public function getTable($name, $refresh = false)
    {
        if ($refresh === false && $this->_tableInstances[$name]) {
            return $this->_tables[$name];
        } else {
            $prefx = $this->_connection->getTablePrefix();
            if (strpos($name, $prefx) === false) {
                $rawName = $prefx . $name;
            } else {
                $rawName = $name;
            }

            
        }
    }
}