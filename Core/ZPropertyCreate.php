<?php
/**
 * ZProperty class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core;

use Z\Z;
use Z\Core\Annotation\ZClassAnnotation;

class ZPropertyCreate
{
    /**
     * 单个类的所有定义在Annotation里面的属性
     * @var array
     */
    private $_properties = array();

    /**
     * 对于单个类中，已经使用的Property，会被缓存在values里面
     * @var array
     */
    private $_values = array();

    /**
     * 只读的属性 
     * @var array
     */
    private $_readOnlies = array();

    /**
     * 本类的Annotation
     * @var \Z\Core\Annotation\ZClassAnnotation
     */
    private $_annotations;

    /**
     * CONSTRUCT METHOD
     * @param  \Z\Core\Annotation\ZClassAnnotation $classAnnotation this class annotation
     * @return \Z\Core\ZProperty 
     */
    public function __construct(ZClassAnnotation $classAnnotation)
    {
        $this->_annotations = $classAnnotation;
        $this->_setProperties();
    }

    /**
     * 获取一个Property
     * @param  string $name property name
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->_values[$name])) {
            return $this->_values[$name];
        }
        if (isset($this->_properties[$name])) {
            return $this->_parseProperty($name);
        }
    }

    /**
     * 解析Properties
     * @return void
     */
    private function _setProperties()
    {
        $annotations = $this->_annotations;

        foreach ($annotations as $key => $value) {
            $this->_properties[$key] = $value;
        }
    }

    private function _parseProperty($name)
    {
        if (!isset($this->_properties[$name])) {
            return null;
        } else {
            if (is_array($this->_properties[$name])) {
                return $this->_parseArrayProperty($name, $this->_properties[$name]);
            } else {
                return $this->_values[$name] = $this->_properties[$name];
            }
        }
    }

    private function _parseArrayProperty($name, $arrayProperty)
    {
        if (isset($arrayProperty[2])) {
            $readOnly = $arrayProperty[2] === 'readOnly' ? true : false;
        } else {
            $readOnly = false;
        }

        $value = $arrayProperty[0];
        switch ($arrayProperty[1]) {
            case 'integer':
            case 'int':
                $return = (int) $value;
                break;
            case 'string':
                $return = (string) $value;
                break;
            case 'bool':
            case 'boolean': 
                $return = (bool) $value;
            case 'instance':
                $func = $value . '::getInstance';
                $return = (is_callable($func)) ? call_user_func($func) : null;
                break;
            case 'newInstance':
                if (class_exists($value)) {
                    $return = new $value();
                } else {
                    $return = null;
                }
            default:
                break;
        }

        $this->_readOnlies[$name] = $readOnly;
        return $this->_values[$name] = $return;
    }
}