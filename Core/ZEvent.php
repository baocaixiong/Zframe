<?php
/**
 * ZEvnet class
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

class ZEvent implements ZEventInterface
{
    /**
     * event handler 
     * @var mixed instance of ZEvent and sub ZEvent
     */
    private $_handler = null;

    /**
     * event name
     * @var String
     */
    public $eventName = '';

    public function addHandler($handler)
    {
        if (is_callable($handler)) {
            $this->_handler = $handler;
        } else {
            throw new ZException('this handler ' . $handler . ' is not callable');
        }
    }

    /**
     * whether has event handler
     * @return boolean [description]
     */
    public function hasHandler()
    {
        if (is_null($this->_handler)) {
            return false;
        }
        return true;
    }
    /**
     * get handler 
     * @return mixed
     */
    public function getHandler()
    {
        return $this->_handler;
    }
}