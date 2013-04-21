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

use \Z\Z;

class ZWebRouter extends ZRouterAbstract
{
    const GET_FORMAT = 'get';
    const PATH_FORMAT = 'path';

    public $rules = [];
    public $urlSuffix = '';
    public $showScriptName = true;
    public $routeVar = 'r';
    public $cacheId = 'cache';
    private $_urlFormat = self::GET_FORMAT;

    //private $_baseUrl;
    public function initialize()
    {
        parent::initialize();
        $this->processRules();
    }
    /**
     * 处理request 获得url
     * @param  \ZRequestInterfase $request ZHttpRequest
     * @return String
     */
    public function parseUrl(\ZRequestInterfase $request)
    {
        $requestUri = $request->getRequestUri();
        
        if ($this->getUrlFormat() === self::PATH_FORMAT) {
            $rawPathInfo = $request->getPathInfo();
            $pathInfo = $this->removeUrlSuffix($rawPathInfo);

        }
    }
    /**
     * 解析路由
     * @return [type] [description]
     */
    public function processRules()
    {
        if (empty($this->rules) || $this->getUrlFormat() === self::GET_FORMAT) {
            return null;
        }
        //url 缓存
        if ($this->cacheId !== false && Z::app()->getComponent($this->cacheId) !== false) {
            //do something
        }

        foreach ($this->rules as $pattern => $route) {
            $this->_rules[] = $this->addRule($pattern, $route);
        }

        if(isset($cache)) { //增加缓存
            $cache->set(self::CACHE_KEY, array($this->_rules, $hash));
        }
    }

    /**
     * 增加路由规则
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    public function addRule ()
    {

    }
    /**
     * 匹配路由规则
     * 这个方法来自接口 ZRouterInterface
     * @return void
     */
    public function matchRule ()
    {
        
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
}