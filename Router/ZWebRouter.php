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
    const GET_FORMAT = 'get';
    const PATH_FORMAT = 'path';

    public $rules = [];
    public $urlSuffix = '';
    public $showScriptName = true;
    public $routeVar = 'r';

    private $_urlFormat = self::GET_FORMAT;

    //private $_baseUrl;
    /**
     * 处理request 获得url
     * @param  \ZRequestInterfase $request ZHttpRequest
     * @return String
     */
    public function parseUrl(\ZRequestInterfase $request)
    {

    }
    /**
     * 解析路由
     * @return [type] [description]
     */
    public function processRules()
    {
        
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