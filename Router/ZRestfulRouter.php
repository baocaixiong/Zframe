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

use Z\Z,
    Z\Exceptions\ZException,
    Z\Collections\Zmap,
    Z\Router\ZRouteNode,
    \AnnotationInterface,
    \ZExecutorInterface;

class ZRestfulRouter extends ZRouterAbstract
{
    const ROUTES_CACHE_KEY = 'z.restful.restfulRouter.cache.key';

    public $routeVar = 'r';

    public $routingPrefix = '/';

    protected $rootRouteNode;
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        //$this->processRules();
    }

    

    public function matchRule($route)
    {

    }

    public function createRule($pattern, $route)
    {

    }

    /**
     * add routes to routeNode
     */
    public function addRoutesToRouteNode()
    {
        $anntations = Z::app()->getAnnotation()->getAnnotations();
        $routeNode = Z::app()->getRouteNode();
        foreach ($anntations as $anntation) {
            if (isset($anntation->root)) {
                foreach ($anntation->getMethods() as $action) {
                    $routeNode->addRoute($action, $this->routingPrefix);
                }
            }
        }

        $this->rootRouteNode = $routeNode;
    }

    public function getRootRouteNode()
    {
        return $this->rootRouteNode;
    }

    /**
     * get router cache key
     * 
     * @return string
     */
    public function getCacheKey()
    {
        return self::ROUTES_CACHE_KEY;
    }
}
