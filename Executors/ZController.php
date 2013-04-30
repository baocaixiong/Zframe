<?php
/**
 * Z mvc controller class
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

use Z\Z;

class ZController extends ZExecutor
{
    /**
     * 构造方法
     *
     * @return \Z\Executors\ZController
     */
    public function __construct()
    {
        $this->application = Z::app();
    }

    public function init ()
    {

    }
    public function execute ()
    {
        var_dump($this->application);
    }
}