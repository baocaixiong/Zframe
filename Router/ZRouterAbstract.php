<?php
/**
 * ZRouter class
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

use \Z\Z,
    \Z\Core\ZAppComponent;

abstract class ZRouterAbstract extends ZAppComponent implements \ZRouterInterface
{
    
    /**
     * 增加路由规则
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    abstract public function createRule ($pattern, $route);
    /**
     * 匹配路由规则
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    abstract public function matchRule ($route);

    abstract public function parseUrl(\ZRequestInterfase $request);


    
}