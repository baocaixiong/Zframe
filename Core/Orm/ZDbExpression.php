<?php
/**
 * ZDbExpression class
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

class ZDbExpression extends ZObject
{
    public $expression;

    public $parameters;

    public function __construct($expression, $parameters)
    {
        $this->expression = $expression;
        $this->parameters = $parameters;
    }

    public function __toString()
    {
        return $this->expression;
    }
}