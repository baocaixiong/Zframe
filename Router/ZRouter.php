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

use Z\Z,
    Z\Exceptions\ZException,
    Z\Collections\Zmap,
    Z\Router\ZRouteNode,
    AnnotationInterface,
    ZExecutorInterface,
    Z\Core\ZAppComponent;

class ZRouter extends ZAppComponent implements \ZRouterInterface
{
    const ROUTES_CACHE_KEY = 'z.restful.restfulRouter.cache.key';
    const GET_FORMAT = 'get';
    const PATH_FORMAT = 'path';
    
    public $urlSuffix = '';

    private $_urlFormat = self::PATH_FORMAT;

    public $routeVar = 'r';

    public $routingPrefix = '/';

    public $expire = 3600;

    protected $rootRouteNode;

    /**
     * add routes to routeNode
     */
    public function addRoutesToRouteNode()
    {
        $anntations = Z::app()->getAnnotation()->getAnnotations();

        $routeNode = Z::app()->getRouteNode();
        foreach ($anntations as $anntation) {
            if ($anntation->get('root')) {
                foreach ($anntation->getMethods() as $action) {
                    $routeNode->addRoute($action, $this->routingPrefix);
                }
            }
        }

        $this->rootRouteNode = $routeNode;
    }

    /**
     * get root route node
     * @return \Z\Router\RouteNode
     */
    public function getRootRouteNode()
    {
        $cacheObject = Z::app()->getCache();

        if (is_null($this->rootRouteNode)) {
            $this->rootRouteNode = $cacheObject->get(self::ROUTES_CACHE_KEY, $this->expire);
            if (!$this->rootRouteNode) {
                $this->addRoutesToRouteNode();
                $cacheObject->set(self::ROUTES_CACHE_KEY, $this->rootRouteNode, $this->expire);
            }
        }

        return $this->rootRouteNode;
    }

    /**
     * 处理request 获得url
     * @param  \ZRequestInterfase $request ZHttpRequest
     * @return String
     */
    public function parseUrl(\ZRequestInterfase $request)
    {
        $requestUri = $request->getRequestUri();
        $routeVar = $this->routeVar;

        if ($this->getUrlFormat() === self::PATH_FORMAT) {
            $rawPathInfo = $request->getPathInfo();
            $pathInfo = $this->removeUrlSuffix($rawPathInfo);
            $route = $pathInfo;
        } elseif(isset($request->getGet()->$routeVar)) {
            $route = $request->getGet()->$routeVar;
        } elseif (isset($request->getPost()->$routeVar)) {
            $route = $request->getPost()->$routeVar;
        } else {
            $route = '';
        }

        return $route;
    }

    /**
     * 移除 后缀
     * @return String 
     */
    public function removeUrlSuffix($pathInfo)
    {
        $urlSuffix = $this->urlSuffix;
        if ($urlSuffix !== '' && substr($pathInfo, -strlen($urlSuffix)) === $urlSuffix) {
            return substr($pathInfo, 0, -strlen($urlSuffix));
        } else {
            return $pathInfo;
        }
    }

    /**
     * 获取urlFormat
     * @return String
     */
    public function getUrlFormat()
    {
        return $this->_urlFormat;
    }

    /**
     * 设置urlFormat 
     * @param 'get' | 'path' $format 两种方式
     * @return void
     */
    public function setUrlFormat($format)
    {
        if ($format === self::PATH_FORMAT || $format === self::GET_FORMAT) {
            $this->_urlFormat = $format;
        } else {
            throw new ZException(Z::t('urlFormat must be "get" or "path"'));
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
