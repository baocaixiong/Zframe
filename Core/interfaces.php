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
namespace Z\Core;
/**
 * ZApplication Interface
 *
 * @author  baocaixiong <baocaixiong@gmail.com>
 * @package system/core
 * @since   v0.1
 */
interface ZApplicationInterface
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
    public function attach($eventName, ZEvent $event);
    public function detach($eventName);
    public function notify();
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