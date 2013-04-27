<?php
/**
 * Z Behavior class
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

class ZBehavior extends Zcore implements \ZBehaviorInterface
{
    /**
     * 是否可用
     * @var boolean
     */
    private $_enabled=false;

    /**
     * 该行为的所有者
     * @var Mixed
     */
    private $_owner;

    /**
     * 此方法需要重载 
     * 将会返回组成行为的事件集合
     * @return Array
     */
    public function events()
    {
        return array();
    }

    /**
     * 添加一个行为
     * @param \Z\Core\ZCore $owner 行为的所有者，必须是ZCore的子类
     * @return [type]        [description]
     */
    public function attach(ZCore $owner)
    {
        $this->_enabled=true;
        $this->_owner=$owner;
        $this->_attachEventHandlers();
    }

    /**
     * 删除行为
     * @param \Z\Core\ZCore $owner 行为的所有者
     * @return void
     */
    public function detach(ZCore $owner)
    {
        foreach($this->events() as $event=>$handler){
            $owner->detachEventHandler($event,array($this,$handler));
        }
        $this->_owner=null;
        $this->_enabled=false;
    }

    /**
     * 设置行为的状态
     * 如果要设置的状态和当前状态不同，那么 true表示开启行为并且将本行为所有的事件都添加到这个行为上面来
     * 为false是要删除所有的事件
     * @param Mixed $value 想要设置的值(始终会转为bool值)
     */
    public function setEnabled($value)
    {
        $value = (bool)$value;
        if ($this->_enabled !== $value && $this->_owner) {
            if($value)
                $this->_attachEventHandlers();
            else
            {
                foreach($this->events() as $event=>$handler)
                    $this->_owner->detachEventHandler($event,array($this,$handler));
            }
        }
        $this->_enabled = $value;
    }

    /**
     * 本行为是否可用
     * @return boolean 本行为可用时 true | false
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * 获得本行为的所有者
     * @return \Z\Core\ZCore
     */
    public function getOwner()
    {
        return $this->_owner;
    }

    /**
     * 将本行为所有的事件都添加到这个行为上面来
     * @return void
     */
    private function _attachEventHandlers()
    {
        $class = new \ReflectionClass($this);
        foreach ($this->events() as $event => $handler) {
            if($class->getMethod($handler)->isPublic())
                $this->_owner->attachEventHandler($event,array($this, $handler));
        }
    }
}