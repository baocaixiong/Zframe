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
     * @param \Z\Router\ZRouteNode $routeNode [description]
     * @param AnnotationInterface  $am        [description]
     */
    public function addRoutesToRouteNode(ZRouteNode $routeNode, AnnotationInterface $am)
    {
        $anntations = $am->collect();

        foreach ($anntations as $anntation) {
            foreach ($anntation->getMethods() as $action) {
                $routeNode->addRoute($this, $action, $this->routingPrefix);
            }
        }
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
