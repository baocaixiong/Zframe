<?php
/**
 * ZExecutorBehavior  class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Executors
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT<>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Executors;

use Z\Z,
    Z\Core\ZBehavior;

class ZExecutorBehavior extends ZBehavior
{
    public function events()
    {
        return array_merge(parent::events(), array(
            'onBeforeDispatch'=>'beforeDispatch',
        ));
    }

    public function beforeDispatch()
    {
        echo 13;
    }
}