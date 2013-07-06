<?php
/**
 * ZObject class
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
use Z\Exceptions\ZUnknownMethodException;
use Z\Exceptions\ZUnknowPropertyException;
use Z\Exceptions\ZInvalidCallException;
use Z\Core\Annotation\ZClassAnnotation;

class ZObject
{
    /**
     * annotation properties
     * @var \Z\Core\ZProperty
     */
    private $_properties;

    /**
     * 初始化方法
     * @return void
     */
    public function init() 
    {
        
    }

    /**
     * 添加默认事件
     * @return void
     */
    protected function addDefaultEvent()
    {
        /**
         * 初始化事件
         * @var [type]
         */
        $rfClass = new \ReflectionClass($this);
        foreach ($rfClass->getConstants() as $key => $value) {
            if (strncasecmp($key, 'event', 5) === 0) {
                $this->on($value);
            }
        }
    }

    /**
     * MEGIC METHOD
     * @param  string $name [description]
     * @throws \Z\Exceptions\ZUnknowPropertyException
     * @return void
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter($name);
        } elseif (!is_null($this->_properties) && ($return = $this->_properties->get($name))) {
            return $return;
        }

        throw new ZUnknowPropertyException(
            Z::t('不存在属性 {class}::{property}', array('{class}' => get_class($this), '{property}' => $name))
        );
    }

    /**
     * MEGIC METHOD
     * 
     * @param  string $name  [description]
     * @param  mixed  $value [description]
     * @throws \Z\Exceptions\ZInvalidCallException
     * @throws \Z\Exceptions\ZUnknowPropertyException
     * @return void
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new ZInvalidCallException(
                Z::t('属性不可写 {class}::{property}', array('{class}' => get_class($this), '{property}' => $name))
            );
        } else {
            throw new ZUnknowPropertyException(
                Z::t('不存在属性 {class}::{property}', array('{class}' => get_class($this), '{property}' => $name))
            );
        }
    }

    /**
     * MEGIC METHOD
     * @param  string  $name [description]
     * @return boolean
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }

    /**
     * MEGIC METHOD
     * @param  string $name [description]
     * @throws \Z\Exceptions\ZInvalidCallException
     * @return void
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $getter)) {
            $this->setter(null);
        } else {
            throw new ZInvalidCallException(
                Z::t(
                    'UNSET一个不可写的属性 {class}::{property}',
                    array('{class}' => get_class($this), '{property}' => $name)
                )
            );
        }
    }

    /**
     * MEGIC METHOD
     * @param  string $name   [description]
     * @param  array  $params [description]
     * @throws \Z\Exceptions\ZUnknownMethodException
     * @return void
     */
    public function __call($name, $params)
    {
        $getter = 'get' . $name;

        if (method_exists($this, $name)) {
            $rfClass = new \ReflectionMethod($this, $name);
            if ($rfClass->isProtected() || $rfClass->isPrivate()) {
                throw new ZUnknownMethodException(
                    Z::t('受保护的方法 {class}::{method}', array('{class}' => get_class($this), '{method}' => $name))
                );
            }
        }

        if (method_exists($this, $getter)) {
            $fn = $this->$getter();
            if ($fn instanceof \Closure) {
                return call_user_func_array($fn, $params);
            }
        }
        throw new ZUnknownMethodException(
            Z::t('未知方法 {class}::{method}', array('{class}' => get_class($this), '{method}' => $name))
        );
    }

    /**
     * 检测一个属性是否存在
     * @param  string  $name     要检测的属性名称
     * @param  boolean $checkVar 是否检测class的属性
     * @return boolean
     */
    public function hasProperty($name, $checkVar = true)
    {
        return $this->canGetProperty($name, $checkVar) || $this->canSetProperty($name, false);
    }

    /**
     * 属性是否可读
     * @param  string  $name     [description]
     * @param  boolean $checkVar [description]
     * @return boolean
     */
    public function canGetProperty($name, $checkVar = true)
    {
        return method_exists($this, 'get' . $name) || $checkVar && property_exists($this, $name);
    }

    /**
     * 属性是否可写
     * @param  string  $name     [description]
     * @param  boolean $checkVar [description]
     * @return boolean
     */
    public function canSetProperty($name, $checkVar = true)
    {
        return method_exists($this, 'set' . $name) || $checkVar && property_exists($this, $name);
    }

    /**
     * get own methods
     * @param  \ReflectionClass $rfClass reflection class
     * @param  int              $filter  reflectionMethod type
     * @return array
     */
    public function getOwnMethods(\ReflectionClass $rfClass, $filter = \ReflectionMethod::IS_PUBLIC)
    {
        $result = array();
        foreach ($rfClass->getMethods($filter) as $reflectionMethod) {
            try {
                $reflectionMethod->getPrototype();
            } catch (\Exception $ex) {
                if ($reflectionMethod->class === $rfClass->getName()) {
                    $result[] = $reflectionMethod;
                }
            }
        }

        return $result;
    }

    /**
     * 设置一个类的AnnotationProperty
     * @param \Z\Core\Annotation\ZClassAnnotation $classAnnotation this class annotation
     */
    public function setAnnProperties(ZClassAnnotation $classAnnotation)
    {
        $this->_properties = new ZPropertyCreate($classAnnotation);
    }
}