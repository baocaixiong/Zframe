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

use Z\Core\ZApplication,
    Z\Z;

class ZRestfulApplication extends ZApplication
{
    /**
     * route node 
     * @var \Z\Router\ZRouteNode
     */
    private $_routerResult;
    /**
     * 处理request
     * @return void
     */
    public function processRequest()
    {
        if ($this->catchAllRequest[0] && isset($this->catchAllRequest[1])) {
            $route = $this->catchAllRequest[1];
        } else {
            $route = $this->getRouter()->parseUrl($this->getRequest());
        }

        $resource = $this->getResource($route);
    }

    /**
     * create resource callback
     * 
     * @param  string $route route string
     * @return callback
     */
    public function getResource($route)
    {
        $rootRouteNode = $this->getRouter()->getRootRouteNode();
        $resource = $rootRouteNode->findRouteFor($route);
        var_dump($this->getRequest()->getGet()->getArrayCopy());
        var_dump($resource);
    }

    protected function createResourceRecursively()
    {
        
    }

    /**
     * 获得router node list
     * 
     * @return \Z\Router\RouterNode
     */
    public function getRouteNode()
    {
        return $this->getComponent('routeNode');
    }

    /**
     * 获得 router component 
     * @return \Z\Request\ZRestfulRequset
     */
    public function getRouter()
    {
        return $this->getComponent('router');
    }

    /**
     * 注册系统核心组建
     * @return void
     */
    public function registerCoreComponents()
    {
        $coreComponents = $this->coreComponents();
        $components = array(
            'router' => array(
                'class'     => 'Z\Router\ZRestfulRouter',
            ),
            'routeNode' => array(
                'class' => 'Z\Router\ZRouteNode',
            ),
        );
        $this->setComponents(array_merge($coreComponents, $components));
    }

    /**
     * 初始化应用
     * 
     * @return void
     */
    public function init()
    {
        $this->getRequest();

        //$router = $this->getCahce()->get($this->getRouter()->getCacheKey());
        $router = null;
        if ($router === null) {
            $this->getRouter()->addRoutesToRouteNode();
            //$this->getCache()->set($this->getRouter()->getCacheKey(), $router);
        }
    }


}