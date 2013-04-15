<?php
/**
 * ZModuel class
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

use \Z\Z;

class ZModule extends ZCore
{
    public $behaviors=array();
    private $_id;
    private $_parentModule;
    private $_components = [];
    private $_componentConfig = [];

    public function __construct($id, $parent, $config=null)
    {
        $this->_id=$id;
        $this->_parentModule=$parent;

    }
    /**
     * set configure
     * @param Array $config config array
     */
    public function setConfig($config)
    {
        //$class = new \ReflectionClass($this);
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
     * 设置组建
     * @param String                                       $id        组建名称id
     * @param Array|\Z\Core\ZApplicationComponentInterface $component 
     * 组建对象或者是组建的配置数组  
     * @return null
     */
    public function setComponent($id, $component)
    {
        if (null === $component) {
            unset($this->_component[$id]);
            return null;
        } elseif ($component instanceof ZApplicationComponentInterface) {
            $this->_components[$id]=$component;
            if (!$component->getIsInitialized()) {
                $component->init();
            }
            return null;
        } elseif (isset($this->_components[$id])) {
            if (isset($component['class']) 
                && $component['class'] !== get_class($this->_components[$id])) {
                unset($this->_components[$id]);
                $this->_componentConfig[$id] = $component; 
                return null;
            }
            foreach ($component as $key => $value) {
                if ($key!=='class') {
                    $this->_components[$id]->$key = $value;
                }
            }
        } elseif (isset($this->_componentConfig[$id]['class'], $component['class'])
            && $this->_componentConfig[$id]['class'] !== $component['class']) {
            $this->_componentConfig[$id] = $component;
            return null;
        }
        if(isset($this->_componentConfig[$id]) && $merge) {
            $this->_componentConfig[$id]=\Z\Helpers\ZMap::mergeArray($this->_componentConfig[$id],$component);
        } else {
            $this->_componentConfig[$id]=$component;
        }
    }

    /**
     * 批量设置组件
     * @param Array $components 一个组建集合的数组
     */
    public function setComponents($components)
    {
        foreach ($components as $id => $component) {
            $this->setComponent($id, $component);
        }
    }

    /**
     * 创建一个组件对象，然后初始化
     * @param String $id            组件名称
     * @param Boolean $createIfNull 是否创建在null的时候
     * @return \Z\Core\ZCore 
     */
    public function getComponent($id, $createIfNull=true)
    {
        if (isset($this->_components[$id])) {
            return $this->_components[$id];
        } elseif (isset($this->_componentConfig[$id]) && $createIfNull) {
            $config = $this->_componentConfig[$id];
            if (!isset($config['enabled']) || $config['enabled']) {
                //Yii::trace("Loading \"$id\" application component",'system.CModule');
                unset($config['enabled']);
                $component=Z::createComponent($config);
                $component->init();
                return $this->_components[$id]=$component;
            }
        }
    }

}