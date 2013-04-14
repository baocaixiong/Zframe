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

use \Z\Z;

class ZCore implements ZCoreInterface
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
        Z::throwZException(
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
        $seter = 'set' . $name;
        if (isset($setter)) {
            return $this->$setter($name, $value);
        } elseif (strncasecmp($name, 'on', 2)===0 && method_exists($this, $name)) {
            $name=strtolower($name);
            if(!isset($this->_events[$name]))
                $this->_events[$name]=new ZList;
            return $this->_events[$name]->add($value);
        } elseif (is_array($this->_behavior)) {
            foreach ($this->_behavior as $behavior) {
                if($behavior->getEnabled() &&
                (property_exists($behavior, $name) || $behavior->isReadProperty($name)))
                    return $behavior->$name=$value;
            }
        }
        if (method_exists($this, 'get' . $name)) {
            Z::throwZException(
                Z::t(
                    'Property {class}.{property} is read only.',
                    ['{class}' => get_class($this), '{property}' => $name]
                )
            );
        }

        Z::throwZException(
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
    
    
}