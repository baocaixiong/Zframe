<?php
/**
 * Z web applcation class
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
namespace Z\Applications;

use Z\Core\ZApplication;

class ZWebApplication extends ZApplication
{
    public function  __construct($config)
    {
        parent::__construct($config);
    }

    
    public function processRequest()
    {
        var_dump($this->router);
    }

    /**
     * 获得路由组件
     * @return \Z\Router\Router  [description]
     */
    public function getRouter()
    {
        return $this->getComponent('router');
    }
    
    /**
     * 注册系统核心组建
     * @return void
     */
    protected function registerCoreComponents()
    {
        $components = array(
            'router' => array(
                'class' => 'Z\Router\ZWebRouter',
            ),
        );
        
        $this->setComponents($components);
    }
}