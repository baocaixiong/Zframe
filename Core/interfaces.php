<?php
/**
 * application interface
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   v0.1
 * @link      http://www.baocaixiong.com
 */

/**
 * ZApplication Interface
 *
 * @author  baocaixiong <baocaixiong@gmail.com>
 * @package system/core
 * @since   v0.1
 */
interface ZApplicationComponentInterface
{
    /**
     * interface method init this application
     */
    public function init();

    /**
     * @return boolean whether the {@link init()} method has been invoked.
     */
    public function getIsInited();
}

/**
 * ZCoreInterface
 *
 * @author  baocaixiong <baocaixiong@gmail.com>
 * @since   v0.1
 */
interface ZCoreInterface
{
    public function __get($name);
    public function __set($name, $value);
    /**
     * add a event 
     * @return [type] [description]
     */
    public function on($name, $handler, $data = array());
    public function off($name, $handler = null);
    public function fire($name, $event = null);
}

interface ZEventInterface
{
    
}

interface ZConfigureRegisterInterface
{
    
    public function setConfig($option, $optionValue = null);

    public function getConfig($optionName = null, $default = null);
}
interface ZBehaviorInterface
{
    /**
     * 增加一个行为对象组建
     * @param \Z\Core\ZCore $component 一个可以增加的组建
     */
    public function attach(ZCoreInterface $component);
    /**
     * 删除一个行为组建
     * @param \Z\Core\ZCore $component  $component 一个可以删除的组建
     */
    public function detach(ZCoreInterface $component);
    /**
     * @return boolean 行为是否可用
     */
    public function getEnabled();
    /**
     * @param boolean $value 设置行为可用
     */
    public function setEnabled($value);
}
interface ZRouterInterface
{
    
}
interface ZRequestInterfase
{
    public function getMethod();
    public function getRawBody();
    public function getContentType();
}

interface ZExecutorInterface
{
    public function init(\ZDispatchContextInterface $dispatch);
    public function execute(\ZDispatchContextInterface $dispatch);
}
interface ZResponseInterface
{
    public function getAllHeaders();

    public function setHeader($headerName, $replace=false);

    public function setStatusCode($code);
}


interface AnnotationInterface
{
    /**
     * 收集annotations
     */
    public function getAnnotations();
}

interface ZParseCommentInterface
{
    public function parse($comment);
}
interface ZDispatchContextInterface
{
    public function assignment(
        \ZRequestInterfase $request, \ZExecutorInterface $executor, $actionId
    );
}

/**
 * ZCaching interface 
 */
interface ZCachingInterface
{
    /**
     * get cache value
     * @param string $key     cache key
     * @param mixed  $default if $key is not exist return $default
     * @return mixed cache value
     */
    public function get($key, $default);

    /**
     * add a cache value
     * @param  string $key     cache key
     * @param  mixed  $value   cache value
     * @param  int    $expires expires time seconds
     */
    public function add($key, $value, $expires = 0);

    /**
     * remove a cache 
     * @return void
     */
    public function remvoe($key);
}