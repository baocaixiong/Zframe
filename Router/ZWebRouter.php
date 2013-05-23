<?php
/**
 * ZWebRouter class
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
    Z\Collections\Zmap;

class ZWebRouter extends ZRouterAbstract
{
    const CACHE_KEY = 'z.webrRouter.cache';
    
    public $rules = array();

    public $showScriptName = true;
    public $scriptName = '.html';
    
    public $routeVar = 'r';
    public $cacheId = 'cache';
    public $caseSensitive = false;

    private $_rules;

    private $_pattern;

    private $_ruleValue;


    //private $_baseUrl;
    /**
     * 解析路由
     */
    public function initialize()
    {
        parent::initialize();
        $this->processRules();
    }
    
    /**
     * 预处理路由器
     * @return [type] [description]
     */
    public function processRules()
    {
        if (empty($this->rules)) {
            return null;
        }
        //url 缓存
        // if ($this->cacheId !== false && Z::app()->getComponent($this->cacheId) !== false) {
        //     //do something $cache = ...
        // }

        foreach ($this->rules as $pattern => $route) {
            $this->_rules[] = $this->createRule($pattern, $route);
        }

        // if(isset($cache)) { //增加缓存
        //     $cache->set(self::CACHE_KEY, array($this->_rules, $hash));
        // }
    }

    /**
     * 增加路由规则
     * @param array   $rules  要增加的路由规则
     * @param boolean $append 是否是append
     */
    public function addRule ($rules, $append = felse)
    {
        if ($append) {
            foreach($rules as $pattern => $route) {
                $this->_rules[] = $this->createUrlRule($pattern, $route);
            }
        } else {
            $rules = array_reverse($rules);
            foreach ($rules as $pattern => $route) {
                array_unshift($this->_rules, $this->createUrlRule($pattern, $route));
            }
        }
    }


    /**
     * 创建一个路由规则
     * 这个方法来自接口 ZRouterInterface
     * @param  string $pattern 路由正则
     * @param  string $route   匹配路由
     * @return void
     */
    public function createRule ($pattern, $routeValue)
    {
        if ($pattern === '' || $routeValue === '') {
            return null;
        }

        if (strncasecmp($pattern, '/', 1) !== 0) {
            throw new ZException(Z::t('rule pattern must be start with "/"'));
        }

        $routeValue = '/' . trim($routeValue, '/');

        $key = md5($routeValue);

        if (!isset($this->_pattern[$key])) {
            $this->_pattern = new Zmap;
            $this->_ruleValue = new Zmap;
        }

        $this->_pattern[$key] = $pattern;
        $this->_ruleValue[$key] = $routeValue;

        return array($this->_pattern, $this->_ruleValue);
    }

    /**
     * 创建一个url
     * @param  string $route     url
     * @param  array  $urlParams url的参数
     * @return string 处理好的url
     */
    public function createUrl($route, $urlParams = array())
    {
        $route = '/' . trim($route, '/');
        $key = md5($route);

        $params = array();
        if ($this->_ruleValue->exists($key)) {
            $route = preg_replace_callback('@/:([_a-z]+)@i', function ($matches) use (&$params) {
                    $params[] = $matches[1];
                    return '';
                }, $this->_pattern[$key]);
        }

        $queryString = array();
        $routeArray = array();
        foreach ($urlParams as $key => $value) {
            if (in_array($key, $params)) {
                $routeArray[$key] = $value;
            } else {
                $queryString[$key] = $value;
            }
        }

        $route .= '/' . trim(implode('/', $routeArray), '/');

        if ($this->getUrlFormat() === self::PATH_FORMAT) {
            return $route . '?' . http_build_query($queryString);
        } else {
            $route = trim($route, '/');
            return '?' . $this->routeVar . '=' . $route . '&' .  http_build_query($queryString);
        }
    }

    /**
     * 匹配路由规则
     * <code>
     * '/:id' => 'site/index' => $request->getParams()return ['id' => 'XXX'];
     * </code>
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    public function matchRule ($routeStr)
    {
        if (empty($routeStr)) {
            return '';
        }

        $routeStr = '/' . trim($routeStr, '/');

        if (isset($this->rules[$routeStr])) {
            return $this->rules[$routeStr];
        } else {
            $request = Z::app()->getRequest();
            foreach ($this->rules as $pattern => $routeValue) {
                if (false === strpos($pattern, ':')) { //如果定义个的路由没有:，说明不是正则路由
                    continue;
                }

                $pattern = '/' . trim($pattern, '/');

                $params = array();
                $route = preg_replace_callback('@:([_a-z]+)@i', function ($matches) use (&$params) {
                    $params[] = $matches[1];
                    return '%';
                }, $pattern);

                $route = str_replace('%', '([^\/]+)', preg_quote($route));
                if (preg_match('@^' . $route . '$@', $routeStr, $matches)) {
                    array_shift($matches);
                    if (!empty($params)) {
                        $params = array_combine($params, $matches);
                        $request->setParams($params);
                    }

                    return $routeValue;
                }
                return $routeStr;
            }
        }
    }
}