<?php
/**
 * Z mvc controller class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Application
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT<>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Executors;

use Z\Z;

class ZController
{
    protected $app;

    public function init ()
    {

    }

    public function run ($actionId)
    {
        var_dump($actionId);
    }
    /**
     * 构造方法
     *
     * @return \Z\Executors\ZController
     */
    public function __construct()
    {
        $this->app = Z::app();
    }
}