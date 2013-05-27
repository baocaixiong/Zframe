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
use Z\Exceptions\ZException;
/**
 * route node class 
 */
class ZRouteNode extends ZAppComponent
{
    protected $methods = array();

    protected $condition = '';
    protected $staticChildren;
    protected $dynamicChildren;

    public $paramFormat;
    /**
     * 初始化组件
     * 
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    public function addRoute($app, $route, $prefix)
    {
        if (!isset($route->path) || empty($route->path)) {
           return;
        } 

        if (!isset($route->method) || empty($route->method)) {
            return;
        }

        $route->path = $prefix . trim($route->path, '/');

        $pathParts = $this->_getRevesedPathParts($route->path);
        
        $this->_addRouteRecursively($pathParts, count($pathParts) - 1, $route);
    }

    private function _addRouteRecursively(&$pathParts, $index, $route)
    {
        if($index < 0) {
            if (is_array($route->method)) {
                $methods = $route->method;
            } else {
                $methods = (array)$route->method;
            }
            foreach($methods as $method) {
                if(isset($this->methods[$method])) {
                    throw new ZException($method . ' ' . str_replace('//','/',$route->path));
                }
                $this->methods[$method] = new ZRt($route);
            }
            return;
        }

        $nextPart = $pathParts[$index];
        
        $matchs = array();

        if(!preg_match('%<\$(.*:.*)>%', $nextPart, $matchs)) {
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
        
        if(!isset($childrenArray[$nextKey])) {
            $child = new ZRouteNode();
            if($isParam) {
                $child->condition = $nextKey;
            }
            $childrenArray[$nextKey] = $child;
        } else {
            $child = $childrenArray[$nextKey];
        }
        $child->paramFormat = $paramFormat;

        $child->_addRouteRecursively($pathParts, $index - 1, $route);
    }

    public function findRouteFor($request) {
        $pathParts = array_reverse(explode('/', $request->getPathInfo()));

        return $this->_findRouteRecursively($pathParts, count($pathParts) - 1, $request->getMethod());
    }
    
    /**
     * 查找resource
     * 
     * @param array  $pathParts Part of a path in reverse order.
     * @param int    $index     Current index of path part array - decrements with each step.
     * @param string $method    HTTP METHOD
     * 
     * @return RoutingResult
     */
    private function _findRouteRecursively(&$pathParts, $index, &$method)
    {
        if($index < 0) {
            $result = new ZRoutingResult();
            if(!empty($this->methods)) {
                if(isset($this->methods[$method])) {
                    $result->routeExists = true;
                    $result->methodIsSupported = true;
                    $result->route = $this->methods[$method]->toRoute();
                } else {
                    $result->routeExists = true;
                    $routes = array_values($this->methods);
                    $result->route = $routes[0]->toRoute();
                    $result->methodIsSupported = false;
                }
                $result->route->methods = array_values($this->methods);
                $result->acceptableMethods = array_keys($this->methods);
            } else {
                $result->routeExists = false;
            }
            return $result;
        }
        
        // Find a child for the next part of the path.
        $nextPart = &$pathParts[$index];

        $result = new ZRoutingResult();
        
        // Check for a static match
        if(isset($this->staticChildren[$nextPart])) {
            $child = $this->staticChildren[$nextPart];
            $result = $child->_findRouteRecursively($pathParts, $index - 1, $method);
        }

        //Check for a dynamic match
        if(!$result->routeExists && !empty($this->dynamicChildren)) {
            foreach($this->dynamicChildren as $child) {
                if($child->matches($nextPart)) {
                    $result = $child->_findRouteRecursively($pathParts, $index - 1, $method);
                    if($result->routeExists) {
                        if($child->condition != '') {
                            $result->arguments[$child->condition] = $child->paramFormat(urldecode($nextPart));
                        }
                        return $result;
                    }
                }
            }
        }
        
        return $result;
    }

    public function matches($path) {
        return $path != '';
    }

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

    public function paramFormat($data)
    {
        switch ($this->paramFormat) {
        case 'int':
            return (int)$data;
        case 'bool':
            return (bool)$data;
        case 'array':
        case 'string':
        default:
            return $data;
        }
    }
}