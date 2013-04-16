<?php
/**
 * ZWebRouter class
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

use \Z\Z;

class ZWebRouter extends ZRouter
{

    public $catchAllRequest;
    /**
     * 初始化路由
     * @return void
     */
    public function initialize()
    {
        $this->_isInited = true;
        $this->processRules();
    }

    /**
     * 解析路由
     * @return [type] [description]
     */
    public function processRules()
    {

    }
    /**
     * 是否已经初始化
     * @return boolean 已经初始化true|false
     */
    public function getIsInited()
    {
        return $this->_isInited;
    }
    /**
     * 增加路由规则
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    public function addRule ()
    {

    }
    /**
     * 匹配路由规则
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    public function matchRule ()
    {

    }
}