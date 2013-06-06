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
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core;

use Z\Z;
use Z\Collections\ZList;
use Z\Exceptions\ZUnknownMethodException;
use Z\Exceptions\ZUnknowPropertyException;
use Z\Exceptions\ZInvalidCallException;

class ZCore extends ZObject implements \ZCoreInterface
{
    /**
     * list of events
     * @var Array
     */
    private $_events = array();

    /**
     * 行为
     * @var Array
     */
    private $_behaviors = array();
    /**
     * __get
     * 
     * @param String $name 想要获得的值
     * 
     * @return Mixed
     * 
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (isset($this->_behaviors[$name])) {
            return $this->_behaviors[$name];
        } elseif (is_array($this->_behaviors)) {
            $this->ensureBehaviors();
            foreach ($this->_behaviors as $behavior) {
                if ($behavior->canGetProperty($name)) {
                    return $behavior->$name;
                }
            }
        }
        throw new ZUnknowPropertyException(
            Z::t('属性不存在: {class}::{property}', array('{class}' => get_class($this), '{property}' => $name))
        );
    }
    /**
     * __set
     * @param String $name 要设置的属性
     * @param Mixed $value 想要设置的值
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        } elseif (is_array($this->_behaviors)) {
            foreach ($this->_behaviors as $behavior) {
                if($behavior->getEnabled() &&
                (property_exists($behavior, $name) || $behavior->isReadProperty($name)))
                    return $behavior->$name = $value;
            }
        }
        if (method_exists($this, 'get' . $name)) {
            throw new ZInvalidCallException(
                Z::t('属性不可写: {class}::{property}', array('{class}' => get_class($this), '{property}' => $name))
            );
        }

        throw new ZUnknowPropertyException(
            Z::t('属性不存在: {class}::{property}', array('{class}' => get_class($this), '{property}' => $name))
        );
    }

    /**
     * 添加一个事件句柄
     * 
     * @param string   $name    事件名称
     * @param callback $handler 事件句柄
     * @param array    $data    事件句柄方法或函数所需要的参数
     * 
     * @return void
     */
    public function on($name, $handler = null, $data = array())
    {
        $this->ensureBehaviors();
        if (!isset($this->_events[$name][0]) || is_null($this->_events[$name][0])) {
            $this->_events[$name][0] = method_exists($this, $name) ? array(array($this, $name), $data) : null;
        }
        if (!is_null($handler)) {
            $this->_events[$name][] = array($handler, $data);
        }
    }

    /**
     * 移除事件句柄
     * 
     * @param string   $name    事件名称
     * @param callback $handler 事件句柄
     * 果如$handler为null将删除该事件的所有句柄,即，该事件不会相应。
     * 否则只删除对应句柄
     *
     * notice: from YII2
     * @return boolean
     */
    public function off($name, $handler = null)
    {
        $this->ensureBehaviors();
        if (isset($this->_events[$name])) {
            if (is_null($handler)) {
                $this->_events[$name] = array();
            } else {
                $removed = false;
                foreach ($this->_events[$name] as $i => $event) {//@see {{on}}
                    if ($event[0] === $handler) {
                        unset($this->_events[$name][$i]);
                        $removed = true;
                    }
                }
                if ($removed) {
                    $this->_events[$name] = array_values($this->_events[$name]);
                }
                return $removed;
            }
        }
        return false;
    }

    /**
     * 触发一个事件
     * @param  string $name  事件名称
     * @param  mixed  $event 事件对象
     * @return void       
     */
    public function fire($name, $event = null)
    {
        $this->ensureBehaviors();
        if (!empty($this->_events[$name]) || !$this->hasEventHandler($name)) {
            if (is_null($event)) {
                $event = new ZEvent();
            }
            if ($event->sender === null) {
                $event->sender = $this;
            }
            $event->handled = false;
            $event->name = $name;

            foreach ($this->_events[$name] as $handler) {
                if (!is_null($handler)) {
                    $event->data = $handler[1];
                    call_user_func($handler[0], $event);
                    if ($event instanceof ZEvent && $event->handled) {
                        return;
                    }
                }
                
            }
        }
    }

    /**
     * 检测一个属性是否可写 
     * @param String $property 要检查的属性
     * @return boolean         可写 true 否则 false
     */
    public function isWriteProperty($property)
    {
        if (method_exists($this, 'set' . $property)) {
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
     * 事件是否定义
     * @param String $name 事件名称
     * @return boolean      
     */
    public function hasEvent($name)
    {
        $rfClass = new \ReflectionClass($this);

        return in_array($name, $rfClass->getConstants());
    }

    /**
     * 判断一个事件是否有事件句柄
     * @return boolean void
     */
    public function hasEventHandler($name)
    {
        if ($this->hasEvent($name)) {
            $name=strtolower($name);
            return isset($this->_events[$name]) && count($this->_events[$name]) > 0;
        }
    }

    /**
     * 获得一个事件的句柄列表
     * @param String $name 事件名称
     * @return \Z\Helpers\ZList 
     * @throws \Z\Exceptions\ZException
     */
    public function getEventHandlers($name)
    {
        if ($this->hasEvent($name)) {
            $name=strtolower($name);
            if (!isset($this->_events[$name])) {
                $this->_events[$name] = array();
            }
            return $this->_events[$name];
        } else {
            throw new ZException(
                Z::t(
                    'Event "{class}.{event}" is not defined.',
                    array('{class}' => get_class($this), '{evnet}' => $name)
                )
            );
        }
    }

    /**
     * 批量增加行为
     * @param Array $behaviors 要增加的行为列表
     * @return void
     */
    public function attachBehaviors($behaviors)
    {
        foreach ($behaviors as $name=>$behavior) {
            $this->_attachBehaviorInternal($name, $behavior);
        }
    }

    /**
     * 批量删除行为
     * @return void
     */
    public function detachBehaviors()
    {
        if (!empty($this->_behaviors)) {
            foreach ($this->_behaviors as $name => $behavior) {
                $this->detachBehavior($name);
            }
            $this->_behaviors = array();
        }
    }
    /**
     * 从一个行为中删除当前对象
     * @param String $name 行为名称
     * @return \Z\Core\ZBehavior
     */
    public function detachBehavior($name)
    {
        if (isset($this->_behaviors[$name])) {
            $this->_behaviors[$name]->detach($this);
            $behavior=$this->_behaviors[$name];
            unset($this->_behaviors[$name]);
            return $behavior;
        }
    }

    /**
     * 确保behavior是安全的
     *
     * from YII2
     * @return void
     */
    public function ensureBehaviors()
    {
        if (is_null($this->_behaviors)) {
            $this->_behaviors = array();
            foreach ($this->behaviors() as $name => $behavior) {
                $this->_attachBehaviorInternal($name, $behavior);
            }
        }
    }

    /**
     * 向当前类添加一个行为
     * @param String            $name     行为名称
     * @param \Z\Core\ZBehavior $behavior 行为对象
     * @return \Z\Core\ZBehavior
     */
    private function _attachBehaviorInternal($name, $behavior)
    {
        if (!($behavior instanceof ZBehaviorInterface)) {
            $behavior = Z::createObject($behavior);
        }

        if (isset($this->_behaviors[$name])) {
            $this->_behaviors[$name]->detach();
        }

        $behavior->setEnabled(true);
        $behavior->attach($this);
        return $this->_behaviors[$name] = $behavior;
    }

    /**
     * 设置行为可用
     * @param String $name 行为名称
     * @return void
     */
    public function setBehaviorEnable($name)
    {
        if (isset($this->_behaviors[$name])) {
            $this->_behaviors[$name]->setEnabled(true);
        }
    }

    /**
     * 设置当前类的所有行为可用
     * @return void
     */
    public function setBehaviorsEnable()
    {
        if (!empty($this->_behaviors)) {
            foreach ($this->_behaviors as $behavior) {
                $behavior->setEnabled(true);
            }
        }
    }
    
    /**
     * 设置行为不可用
     * @param String $name 行为名称
     * @return void
     */
    public function setBehaviorDisable($name)
    {
        if (isset($this->_behaviors[$name])) {
            $this->_behaviors[$name]->setEnabled(false);
        }
    }

    /**
     * 设置当前类的所有行为不可用
     * @return void
     */
    public function setBehaviorsDisable()
    {
        if (!empty($this->_behaviors)) {
            foreach ($this->_behaviors as $behavior) {
                $behavior->setEnabled(false);
            }
        }
    }
}