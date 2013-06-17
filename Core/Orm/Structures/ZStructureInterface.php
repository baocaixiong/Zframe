<?php
/**
 * ZStructureInterface
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Structures
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Structures;

interface ZStructureInterface
{
    /**
     * Get primary key of a table
     * @param  string $tableName table name
     * @return string
     */
    function getPrimary($tableName);

    /**
     * Get column holding foreign key(被)
     * @param  string $name      column name
     * @param  string $tableName table name
     * @return string
     */
    function getReferencingColumn($name, $tableName);

    /**
     * Get target table (被)
     * @return string
     */
    function getReferencingTable($name, $table);

    /**
     * Get column holding foreign key
     * @param  string $name      column name
     * @param  string $tableName table name
     * @return string
     */
    function getReferencedColumn($name, $tableName);

    /**
     * 引用的table
     * @param  string $name      
     * @param  string $tableName [description]
     * @return string
     */
    function getReferencedTable($name, $tableName);

    /**
     * 得到序列名称，用来插入数据
     * @param  string $tableName table name
     * @return string
     */
    function getSequence($table);
}