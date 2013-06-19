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
use Z\Core\Orm\ZDbConnection;
use Z\Core\Orm\Structures\ZStructureInterface;
use ZCachingInterface;
use Z\Core\Orm\Structures\ZStructureConvention;

class ZMapper extends ZOrmAbstract
{
    /**
     * table instances
     * 可以用这个静态属性作为所有的mapper(实际是table)之间的父级缓存
     * @var array
     */
    protected static $tableInstances;

    protected $pdo;
    /**
     * CONSTRUCT METHOD
     * @param \Z\Core\Orm\ZDbConnection                  $db        db connection
     * @param \Z\Core\Orm\Structures\ZStructureInterface $structure structure 
     * @param \ZCachingInterface                         $cache     cache
     */
    public function __construct(ZDbConnection $db, ZCachingInterface $cache = null)
    {
        $this->connection = $db;
        $this->pdo = $db->pdo;
        $this->cache = $cache;
        $this->structure = $db->getStructure();

        $this->driverName = $db->getDriverName();
    }

    /**
     * get table instance
     *
     * @param string  $tableName table name
     * @param boolean $single    whether single
     * @return \Z\Core\Orm\ZTable
     */
    public function getTableInstance($tableName = null, $single = false)
    {
        if (is_null($tableName)) {
            $tableName = $this->getTableName();
        }

        if (isset(self::$tableInstances[$tableName])) {
            return self::$tableInstances;
        }

        return new ZTable($tableName, $this->connection, $this->modelClass, $single);

    }

    /**
     * get table name
     * @return string
     */
    protected function getTableName()
    {
        return $this->connection->getTablePrefix() . $this->tableName;
    }

    public function getAll()
    {
        $table = $this->getTableInstance();

        foreach ($table as $row) {
            var_dump($row['title'], $row->author['name']);

        }
    }
}