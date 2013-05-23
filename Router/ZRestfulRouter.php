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
    \AnnotationInterface;

class ZRestfulRouter extends ZRouterAbstract
{
    const ROUTES_CACHE_KEY = 'z.restful.restfulRouter.cache.key';

    public $_urlFormat = 'path';

    public $routeVar = 'r';
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

    public function addRoutesToRouteNode(ZRouteNode $routeNode, AnnotationInterface $am)
    {
        $anntations = $am->collect();
echo "<pre>";
        foreach ($anntations as $anntation) {
            
            print_r($anntation);
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
