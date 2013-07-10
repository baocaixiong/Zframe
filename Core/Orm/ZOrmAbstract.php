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
     * 加上引号
     * @param  mixed $val 
     * @return string
     */
    protected function quote($val)
    {
        if (!isset($val)) {
            return "NULL";
        }
        if (is_array($val)) { // (a, b) IN ((1, 2), (3, 4))
            return "(" . implode(", ", array_map(array($this, 'quote'), $val)) . ")";
        }
        $val = $this->formatValue($val);
        if (is_float($val)) {
            return sprintf("%F", $val); // otherwise depends on setlocale()
        }
        if ($val === false) {
            return "0";
        }
        if (is_int($val) || $val instanceof ZDbExpression) { // number or SQL code - for example "NOW()"
            return (string) $val;
        }
        return $this->connection->pdo->quote($val);
    }

    protected function removeExtraDots($expression) {
        return preg_replace('@(?:\\b[a-z_][a-z0-9_.:]*[.:])?([a-z_][a-z0-9_]*)[.:]([a-z_*])@i', '\\1.\\2', $expression); // rewrite tab1.tab2.col
    }
}