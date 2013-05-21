<?php
/**
 * ZRestfulRouter class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Router
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Router;

use Z\Z;
use \Z\Core\ZAppComponent;
/**
 * route node class 
 */
class ZRouteNode extends ZAppComponent
{
    protected $methods = array();

    /**
     * 初始化组件
     * 
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }
}