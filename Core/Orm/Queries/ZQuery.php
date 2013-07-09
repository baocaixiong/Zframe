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
     * ZTable的实例
     * @var \Z\Core\Orm\ZTable
     */
    protected $table;

    /**
     * 本Table的Schema,为了方便
     * @var Z\Core\Orm\Schema\ZTableSchema
     */
    protected $tableSchema;
}