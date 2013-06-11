<?php
/**
 * ZRoute class
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

use Z\Z;
use \Z\Core\ZAppComponent;
use Z\Exceptions\ZRouterException;
use Z\Core\Annotation\ZMethodAnnotation;
/**
 * route node class 
 */
class ZRouteNode extends ZAppComponent
{
    /**
     * Route http method
     * @var array
     */
    protected $methods = array();

    /**
     * 动态route的key
     * @var string
     */
    protected $condition = '';

    /**
     * route的静态子route
     * @var [type]
     */
    protected $staticChildren;

    /**
     * route的动态子route
     * @var [type]
     */
    protected $dynamicChildren;

    /**
     * route的格式化方式 int string 
     * @var [type]
     */
    protected $paramFormat;

    /**
     * add route
     * @param \Z\Core\Annotation\ZMethodAnnotation $route  [description]
     * @param string                               $prefix [description]
     */
    public function addRoute(ZMethodAnnotation $route, $prefix)
    {
        $this->checkResourceMethod($route);
        $route->path = $prefix . trim($route->path, '/');

        $pathParts = $this->_getRevesedPathParts($route->path);
        
        $this->_addRouteRecursively($pathParts, count($pathParts) - 1, $route);
    }

    /**
     * recursively add route 
     * @param array                                $pathParts pathinfo array
     * @param int                                  $index     Current index of path part array
     * @param \Z\Core\Annotation\ZMethodAnnotation $route     method annotation
     */
    private function _addRouteRecursively(&$pathParts, $index, ZMethodAnnotation $route)
    {
        if ($index < 0) {
            if (is_array($route->method)) {
                $methods = $route->method;
            } else {
                $methods = (array)$route->method;
            }

            foreach ($methods as $method) {
                $this->methods[$method] = $route;
            }
            return;
        }

        $nextPart = $pathParts[$index];
        $matchs = array();

        if (!preg_match('%<\$(.*:.*)>%', $nextPart, $matchs)) {
            $childrenArray = &$this->staticChildren;
            $nextKey = $nextPart;
            $isParam = false;
            $paramFormat = false;
        } else {
            $childrenArray = &$this->dynamicChildren;
            $temp = explode(':', $matchs[1]);
            $nextKey = $temp[0];
            $paramFormat = $temp[1];
            $isParam = true;
        }
        
        if (!isset($childrenArray[$nextKey])) {
            $child = new self();
            if ($isParam) {
                $child->condition = $nextKey;
            }
            $childrenArray[$nextKey] = $child;
        } else {
            $child = $childrenArray[$nextKey];
        }
        $child->paramFormat = $paramFormat;

        $child->_addRouteRecursively($pathParts, $index - 1, $route);
    }

    /**
     * find route 
     * @param  string $pathinfo path info
     * @return \Z\Router\ZRoutingResult
     */
    public function findRouteFor($pathinfo)
    {
        $pathParts = array_reverse(explode('/', $pathinfo));

        $method = Z::app()->getRequest()->getMethod();
        return $this->_findRouteRecursively(
            $pathParts, count($pathParts) - 1, $method
        );
    }
    
    /**
     * 查找resource
     * 
     * @param array  $pathParts path parts
     * @param int    $index     Current index of path part array
     * @param string $method    HTTP METHOD
     * 
     * @return RoutingResult
     */
    private function _findRouteRecursively(
        &$pathParts, $index, &$method, ZRoutingResult $result = null
    )
    {
        if ($index < 0) {
            if (!empty($this->methods)) {
                if (isset($this->methods[$method])) {
                    $result->routeExists = true;
                    $result->methodIsSupported = true;
                    $result->route = $this->methods[$method];
                    $result->cacheTime = $this->methods[$method]->cacheTime;
                    $result->etag = $this->methods[$method]->etag;
                    $result->response = $this->methods[$method]->response;
                } else {
                    $result->routeExists = true;
                    $routes = array_values($this->methods);
                    //$result->route = $routes[0];
                    $result->methodIsSupported = false;
                }
                //$result->route->methods = array_values($this->methods);
                
                
                $result->acceptableMethods = array_keys($this->methods);
            } else {
                $result->routeExists = false;
            }
            return $result;
        }
        
        // Find a child for the next part of the path.
        $nextPart = &$pathParts[$index];

        if (is_null($result)) {
            $result = new ZRoutingResult();
        }
        
        // 检查静态的路由
        if (isset($this->staticChildren[$nextPart])) {
            $child = $this->staticChildren[$nextPart];
            $result = $child->_findRouteRecursively($pathParts, $index - 1, $method, $result);
        }

        //检查动态路由
        if (!$result->routeExists && !empty($this->dynamicChildren)) {
            foreach ($this->dynamicChildren as $child) {
                if ($nextPart !== '') {
                    $result = $child->_findRouteRecursively(
                        $pathParts, $index - 1, $method, $result
                    );
                    if ($result->routeExists) {
                        if ($child->condition != '') {
                            $result->arguments[$child->condition] =
                            $child->paramFormat(urldecode($nextPart));
                        }
                        return $result;
                    }
                }
            }
        }
        
        return $result;
    }

    /**
     * getRevesedPathParts
     * @param  string $path path info
     * @return array
     */
    private function _getRevesedPathParts($path)
    {
        $parts = explode('/', $path);

        $count = count($parts);
        $return = array();
        for ($i = $count - 1; $i >= 0; $i--) {
            if ($parts[$i] !== '') {
                $return[] = $parts[$i];
            }
        }
        return $return;
    }

    /**
     * parameter format 
     * !path=/test/<$xxx:int>! => 将匹配到的路由做int化
     * 
     * @param  string $pathPart 路由的一部分
     * @return mixed
     */
    protected function paramFormat($pathPart)
    {
        switch ($this->paramFormat) {
        case 'int':
            return (int)$pathPart;
        case 'bool':
            return (bool)$pathPart;
        case 'array':
        case 'string':
        default:
            return $pathPart;
        }
    }

    /**
     * 检查resource的action是否都有正确的annotation
     * @param  ZMethodAnnotation $route [description]
     * @return [type]                   [description]
     */
    protected function checkResourceMethod(ZMethodAnnotation $route)
    {
        if (!isset($route->path) || empty($route->path)) {
            throw new ZRouterException(
                Z::t(
                    'Resource PUBLIC method `{methodName}` must has !path! annotation.',
                    array('{methodName}' => $route->methodName)
                )
            );
        } 

        if (!isset($route->method) || empty($route->method)) {
            throw new ZRouterException(
                Z::t(
                    'Resource PUBLIC method `{methodName}` must has !method! annotation.',
                    array('{methodName}' => $route->methodName)
                )
            );
        }
    }
}