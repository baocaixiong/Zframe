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

use Z\Z;
use \Z\Core\ZAppComponent;
use Z\Exceptions\ZException;
/**
 * route node class 
 */
class ZRouteNode extends ZAppComponent
{
    protected $methods = array();

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
        
        $pathPars = $this->_getRevesedPathParts($route->path);
        
        $this->_addRouteRecursively($pathParts, count($pathParts) - 1, $route);
    }

    private function _addRouteRecursively(&$pathParts, $index, $route)
    {
        if($index < 0) {
            foreach($route->getMethods as $method) {
                if(isset($this->methods[$method])) {
                    throw new ZException($method . ' ' . str_replace('//','/',$route->path), $route->fileDefined, $route->lineDefined);
                }
                $this->methods[$method] = new Rt($route);
            }
            return;
        }

        $nextPart = $pathParts[$index];
        
        if($nextPart[0] != '$') {
            $childrenArray = &$this->s;
            $nextKey = $nextPart;
            $isParam = false;
        } else {
            $childrenArray = &$this->p;
            $nextKey = substr($nextPart, 1);
            $isParam = true;
        }
        
        if(!isset($childrenArray[$nextKey])) {
            $child = new RtNode();
            if($isParam) {
                $child->c = $nextKey;
            }
            $childrenArray[$nextKey] = $child;
        } else {
            $child = $childrenArray[$nextKey];
        }
        
        $child->addRouteRecursively($pathParts, $index - 1, $route);
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
}