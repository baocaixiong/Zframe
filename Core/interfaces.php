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
    public function initialize();

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
    public function attachEventHandler($eventName, $event);
    public function detachEventHandler($eventName);
    public function raiseEvent($name, $event);
}

interface ZEventInterface
{
    /**
     * add event handler
     */
    public function addHandler($handler);
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
    public function attach($component);
    /**
     * 删除一个行为组建
     * @param \Z\Core\ZCore $component  $component 一个可以删除的组建
     */
    public function detach($component);
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
    public function createRule($pattern, $route);

    public function matchRule($route);
}
interface ZRequestInterfase
{
    public function getMethod();
    public function getRawBody();
    public function getContentType();
}

interface ZExecutorInterface
{
    public function execute($actionId);
}
interface ZResponseInterface
{
    public function respond();

    public function output();

    public function setHeader($headerName, $replace=false);

    public function setStatusCode($code);
}