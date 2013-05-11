<?php
/**
 * ZAppComponent class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Request
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core;

class ZAppComponent extends ZCore implements \ZApplicationComponentInterface
{
    protected $_isInited = false;

    /**
     * 初始化组件，将行为添加到组件
     * @return void
     */
    public function initialize()
    {
        $this->attachBehaviors($this->behaviors());
        $this->_isInited = true;
    }

    /**
     * 检查一个组件是否已经初始化
     * @return void
     */
    public function getIsInited()
    {
        return $this->_isInited;
    }

    /**
     * 返回一个行为列表
     * 
     * @return array
     */
    protected function behaviors()
    {
        return array();
    }
}