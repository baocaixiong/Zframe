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
    Z\Z,
    Z\Exceptions\ZHttpException;

class ZRestfulApplication extends ZApplication
{
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

        $resource = $this->runResource($route);
    }

    /**
     * 运行resource
     * @param string $route request path
     * 
     * @return void
     */
    public function runResource($route)
    {
        try {
            $rr = $this->getResource($route);
            list($routeResult, $executor) = $rr;

            $context = $this->getDispatch()
                ->assignment($this->getRequest(), $executor, $routeResult);

            $executor->init($context);
            $executor->executor();
        } catch (ZHttpException $e) {
            echo '404 NOT FOUND';
        }
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
        $routeResult = $rootRouteNode->findRouteFor($route);

        if (is_null($routeResult->route)) {
            throw new ZHttpException(
                Z::t(
                    'Unable to resolve the request "{route}".',
                    array('{route}' => $route === '' ? '' : $route)
                )
            );

        } else {
            $resource = new $routeResult->route->className($this);
        }
        
        return array($routeResult, $resource);
    }

    /**
     * create response
     * @param  string $responseCode response code e.g: http, json, download
     * @return \Z\Response\ZHttpResponse
     */
    public function createResponse($responseCode)
    {
        return \Z\Response\ZResponseFactory::create($responseCode);
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