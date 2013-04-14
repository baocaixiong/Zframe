<?php
/**
 * ZCore class
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

class ZCore implements ZCoreInterface
{
    /**
     * list of events
     * @var Array
     */
    private $_events = [];
    /**
     * [__get description]
     * 
     * @param [type] $name [description]
     * 
     * @return [type]       [description]
     * 
     */
    public function __get($name)
    {

    }
    /**
     * [__set description]
     * @param [type] $name  [description]
     * @param [type] $value [description]
     */
    public function __set($name, $value)
    {

    }

    /**
     * 检测一个属性是否可写 
     * @param String $property 要检查的属性
     * @return boolean         可写 true 否则 false
     */
    public function isWriteProperty($property)
    {
        if (method_exists($this,'set'.$property)) {
            return false;
        } else {
            if (property_exists($this, $property)) {
               $class = new \ReflectionClass($this);
               return $class->getProperty($property)->isPublic();
            }
        }
        return false;
    }

    /**
     * 检测一个属性是否 可读
     * @param String $property 要检查的属性
     * @return boolean         可读 true 否则 false
     */
    public function isReadProperty($property)
    {
        if (method_exists($this, 'get' . $property)) {
            return true;
        } else {
            if (property_exists($this, $property)) {
               $class = new \ReflectionClass($this);
               return $class->getProperty($property)->isPublic();
            }
        }
        return false;
    }
    /**
     * attach a event
     * @param  String $eventName event name
     * @param  ZEvent $event     ZEvent instance or sub ZEvent instance
     * @return void
     */
    public function attach($eventName, ZEvent $event)
    {
        $this->_events[$eventName] = $event;
        $event->eventName = $eventName;
    }
    /**
     * detach a event
     * @param  String $eventName name, you want to detach
     * @return void
     */
    public function detach($eventName)
    {
        if (isset($this->_events[$eventName])) {
            unset($this->_events[$eventName]);
        }
    }
    /**
     * attach event handler 
     * @param  String $name    eventName
     * @param  mixed  $handler a callable method
     * @return void
     */
    public function attachEventHandler($name, $handler)
    {
        $this->getEventByName($name)->addHandler($handler);
    }
    /**
     * attach a event and handler 
     * @param  String $eventName   event name
     * @param  mixed  $eventObject ZEvent instance or sub ZEvent instance
     * @param  mixed  $handler     a callable method
     * @return void
     */
    public function attachEvent($eventName, $eventObject, $handler)
    {
        $this->attach($eventName, $eventObject);
        $this->attachEventHandler($eventName, $handler);
    }

    /**
     * get event by event name
     * @param  String $name event name
     * @return ZEvent 
     * @throw  ZException event not found 
     */
    public function getEventByName($name)
    {
        if (isset($this->_events[$name])) {
            return $this->_events[$name];
        }
        throw new ZException('event not found');
    }

    /**
     * run event 
     * @return void
     * @throw ZException 
     */
    public function runEvent($name, $event)
    {
        $name = strtolower($name);
        if (isset($this->_events[$name])) {
            
        } else {
            throw new ZException('event ' . $name . ' is not defined');
        }        
    }
    /**
     * notify application run event
     * @return void
     */
    public function notify()
    {
        foreach ($this->_events as $event) {
            if ($event->hasHandler()) {
                call_user_func($event->getHandler(), $event);
            }
        }
    }
    /**
     * whether has eventHanlder
     * @param  String  $name eventName
     * @return boolean  
     * true has
     * false no
     */
    public function hasEvnetHanlder($name)
    {
        $name=strtolower($name);
        return isset($this->_events[$name]) && $this->_events[$name]->hasHandler();
    }
}