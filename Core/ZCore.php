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

use \Z\Z,
    \Z\Collections\ZList,
    \Z\Exceptions\ZException;

class ZCore implements \ZCoreInterface
{
    /**
     * list of events
     * @var Array
     */
    private $_events = [];

    /**
     * 行为
     * @var Array
     */
    private $_behavior = [];
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
        } elseif (strncasecmp($name, 'on', 2) === 0 && method_exists($this, $name)) {
            $name=strtolower($name);
            if (!isset($this->_events[$name])) {
                $this->_events[$name] = new ZList();
            }
            return $this->_events[$name];
        } elseif (isset($this->_behavior[$name])) {
            return $this->_m[$name];
        } elseif (is_array($this->_behavior)) {
            foreach ($this->_behavior as $behavior) {
                if($object->behavior() && 
                    (property_exists($behavior, $name) || $object->isReadProperty($name)))
                    return $behavior->$name;
            }
        }
        throw new ZException(
            Z::t(
                'Property "{class}.{property}" is not defined.',
                array('{class}' => get_class($this), '{property}' => $name)
            )
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
        } elseif (strncasecmp($name, 'on', 2)===0 && method_exists($this, $name)) {
            $name=strtolower($name);
            if (!isset($this->_events[$name])) {
                $this->_events[$name]=new ZList;
            }
            return $this->_events[$name]->add($value);
        } elseif (is_array($this->_behavior)) {
            foreach ($this->_behavior as $behavior) {
                if($behavior->getEnabled() &&
                (property_exists($behavior, $name) || $behavior->isReadProperty($name)))
                    return $behavior->$name=$value;
            }
        }
        if (method_exists($this, 'get' . $name)) {
            throw new ZException(
                Z::t(
                    'Property {class}.{property} is read only.',
                    ['{class}' => get_class($this), '{property}' => $name]
                )
            );
        }

        throw new ZException(
            Z::t(
                'Property "{class}.{property}" is not defined.',
                array('{class}' => get_class($this), '{property}' => $name)
            )
        );
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
     * 是否存在一个事件
     * @param String $name 事件名称
     * @return boolean      
     */
    public function hasEvent($name)
    {
        return !strncasecmp($name, 'on', 2) && method_exists($this, $name);
    }

    /**
     * 判断一个事件是否有事件句柄
     * @return boolean void
     */
    public function hasEventHandler($name)
    {
        $name=strtolower($name);
        return isset($this->_events[$name]) && $this->_events[$name]->getCount() > 0;
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
                $this->_events[$name]=new ZList;
            }
            return $this->_events[$name];
        } else {
            throw new ZException(
                Z::t(
                    'Event "{class}.{event}" is not defined.',
                    ['{class}' => get_class($this), '{evnet}' => $name]
                )
            );
        }
    }

    /**
     * 给事件添加一个事件句柄
     * @param String   $name    事件名称
     * @param callback $handler 事件句柄
     * @return void
     * @throws \Z\Exceptions\ZException
     */
    public function attachEventHandler($name, $handler)
    {
        $this->getEventHandlers($name)->add($handler);
    }

    /**
     * 删除一个事件的事件句柄
     * @param String $name 事件名称
     * @return boolean 
     */
    public function detachEventHandler($name)
    {
        if ($this->hasEventHandler($name)) {
            $this->getEventHandlers($name)->remove($handler) !== false;
        } else {
            return false;
        }
    }

    /**
     * 运行一个事件句柄
     * @param String $name 事件名称
     * @return void
     */
    public function raiseEvent($name, $event)
    {
        $name = strtolower($name);
        if (isset($this->_events[$name])) {
            foreach ($this->_events[$name] as $handler) {
                if (is_string($handler)) {
                    call_user_func($handler, $evnet);
                } elseif (is_callable($handler, true)) {
                    if (is_array($handler)) {
                        list($object, $method) = $handler;
                        if (is_string($object)) {
                            call_user_func($handler, $event);
                        } elseif (method_exists($object, $method)) {
                            $object->$method($event);
                        } else {
                            throw new ZException(
                                Z::t(
                                    'Event "{class}.{event}" is 
                                    attached with an invalid handler "{handler}".',
                                    [
                                        '{class}' => get_class($this),
                                        '{event}' => $name,
                                        '{handler}' => $handler[1]
                                    ]
                                )
                            );
                        }
                    } else {
                        call_user_func($handler, $evnet);
                    }
                } else {
                    throw new ZException(
                        Z::t(
                            'Event "{class}.{event}" is 
                            attached with an invalid handler "{handler}".',
                            [
                                '{class}' => get_class($this),
                                '{event}' => $name,
                                '{handler}' => $handler[1]
                            ]
                        )
                    );
                }
                if (($event instanceof ZEvent) && $event->handled) {
                    return;
                }
            }
        } elseif (Z_DEBUG && !$this->hasEvent($name)) {
            throw new ZException(
                Z::t(
                    'Event "{class}.{event}" is not defined.',
                    ['{class}' => get_class($this), '{event}' => $name]
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
            $this->attachBehavior($name, $behavior);
        }
    }

    /**
     * 批量删除行为
     * @return void
     */
    public function detachBehaviors()
    {
        if (!empty($this->_behavior)) {
            foreach ($this->_behavior as $behavior) {
                $this->detachBehavior($name);
            }
            $this->_behavior = [];
        }
    }
    /**
     * 从一个行为中删除当前对象
     * @param String $name 行为名称
     * @return \Z\Core\ZBehavior
     */
    public function detachBehavior($name)
    {
        if (isset($this->_behavior[$name])) {
            $this->_behavior[$name]->detach($this);
            $behavior=$this->_behavior[$name];
            unset($this->_behavior[$name]);
            return $behavior;
        }
    }

    /**
     * 向当前类添加一个行为
     * @param String            $name     行为名称
     * @param \Z\Core\ZBehavior $behavior 行为对象
     * @return \Z\Core\ZBehavior
     */
    public function attachBehavior($name, $behavior)
    {
        if (!($behavior instanceof ZBehaviorInterface)) {
            $behavior = Z::createComponent($behavior);
        }
        $behavior->setEnabled(true);
        $behavior->attach($this);
        return $this->_behavior[$name] = $behavior;
    }

    /**
     * 设置行为可用
     * @param String $name 行为名称
     * @return void
     */
    public function setBehaviorEnable($name)
    {
        if (isset($this->_behavior[$name])) {
            $this->_behavior[$name]->setEnabled(true);
        }
    }

    /**
     * 设置当前类的所有行为可用
     * @return void
     */
    public function setBehaviorsEnable()
    {
        if (!empty($this->_behavior)) {
            foreach ($this->_behavior as $behavior) {
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
        if (isset($this->_behavior[$name])) {
            $this->_behavior[$name]->setEnabled(false);
        }
    }

    /**
     * 设置当前类的所有行为不可用
     * @return void
     */
    public function setBehaviorsDisable()
    {
        if (!empty($this->_behavior)) {
            foreach ($this->_behavior as $behavior) {
                $behavior->setEnabled(false);
            }
        }
    }
}