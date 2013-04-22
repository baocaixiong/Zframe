<?php
/**
 * Z web applcation class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Application
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT<>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Applications;

use Z\Core\ZApplication;

class ZWebApplication extends ZApplication
{
    public $defaultController = 'site';
    public $defaultAction = 'index';
    /**
     * 是否catch所有的reque
     * <code>   
     * 'catchAllRequest' => [
     *     true,'<router>'
     * ];
     * </code>
     * @var boolean
     */
    public $catchAllRequest = [
        false, ''
    ];
    /**
     * 处理request
     * 
     */
    public function processRequest()
    {
        if ($this->catchAllRequest[0] && isset($this->catchAllRequest[1])) {
            $route = $this->catchAllRequest[1];
        } else {
            $route = $this->getRouter()->parseUrl($this->getRequest());
        }

        $this->runController($route);
    }

    /**
     * 运行控制器
     * @param  String $route asdf/asdf
     * @return void
     */
    public function runController ($route)
    {
        var_dump($route);
    }

    /**
     * 获得路由组件
     * @return \Z\Router\Router  [description]
     */
    public function getRouter()
    {
        return $this->getComponent('router');
    }
    
    public function getRequest()
    {
        return $this->getComponent('request');
    }
    /**
     * 注册系统核心组建
     * @return void
     */
    protected function registerCoreComponents()
    {
        $components = array(
            'router' => array(
                'class'     => 'Z\Router\ZWebRouter',
                'urlFormat' => 'path',
                'rules'     => [],
            ),
            'request' => array(
                'class'     => 'Z\Request\ZWebRequest',
                'enableXss' => true,
            ),
        );
        
        $this->setComponents($components);
    }

    public function init()
    {
        $this->getRequest();
    }
}