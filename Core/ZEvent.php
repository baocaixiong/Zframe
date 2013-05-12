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

class ZEvent implements \ZEventInterface
{
    /**
     * 事件是否已经触发
     * @var boolean
     */
    public $handled = false;

    /**
     * 事件的触发者
     * @var \Z\Core\ZCore
     */
    public $sender;

    /**
     * @var mixed additional event parameters.
     */
    public $params;

    /**
     * construct method
     * @param \Z\Core\ZCore $sender 事件的触发者
     * @param mixed         $params 参数
     */
    public function __construct($sender = null,$params = null)
    {
        $this->sender = $sender;
        $this->params = $params;
    }
}