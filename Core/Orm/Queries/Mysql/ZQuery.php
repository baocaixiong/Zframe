<?php
/**
 * ZQuery class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Queries\Mysql
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Queries\Mysql;

use Z\Core\Orm\Queries\ZQuery as BaseQuery;

class ZQuery extends BaseQuery
{
    public function quoteTableAndColumn($tableOrColumn)
    {
        return '`' . $tableOrColumn . '`';
    }
}
