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
    \Z\Core\ZCore;

abstract class ZRouter extends ZCore implements \ZRouterInterface,\ZApplicationComponentInterface
{
    protected $_isInited = false;
    /**
     * 初始化路由
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    abstract public function initialize ();

    /**
     * 是否已经初始化了
     * @return void
     */
    abstract public function getIsInited();
    /**
     * 增加路由规则
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    abstract public function addRule ();
    /**
     * 匹配路由规则
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    abstract public function matchRule ();


}