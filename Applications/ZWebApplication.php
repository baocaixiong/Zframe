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

use Z\Core\ZApplication,
    Z\Z;

class ZWebApplication extends ZApplication
{
    public $defaultController = 'site';
    public $defaultAction = 'index';
    public $layout='main';

    private $_controllerPath;
    /**
     * 是否catch所有的reque
     * <code>   
     * 'catchAllRequest' => [
     *     true,'<router>'
     * ];
     * </code>
     * @var boolean
     */
    public $catchAllRequest = [
        false, ''
    ];
    /**
     * 处理request
     * 
     */
    public function processRequest()
    {
        if ($this->catchAllRequest[0] && isset($this->catchAllRequest[1])) {
            $route = $this->catchAllRequest[1];
        } else {
            $route = $this->getRouter()->parseUrl($this->getRequest());
        }

        $this->runController($route);
    }

    /**
     * 运行控制器
     * @param  String $route asdf/asdf
     * @return void
     */
    public function runController($route)
    {
        if (($ca = $this->createController($route)) !== null) {
            list($controller, $actionId) = $ca;

            $controller->init();
            $controller->run($actionID);
        } else {
            throw new \Z\Exceptions\ZHttpException(
                Z::t(
                    'Unable to resolve the request "{route}".',
                    ['{route}' => $route === '' ? $this->defaultController : $route]
                )
            );
        }
    }


    /**
     * 创建控制器对象
     * 
     * @param  String $route route
     * @param  Object $owner ..
     * @return Array [Controller, $route]
     */
    public function createController($route, $owner = null)
    {
        if (is_null($owner)) {
            $owner = $this;
        }
        if (($route = trim($route, '/')) === '') {
            $route=$owner->defaultController;
        }
    
        $route .= '/';
        while (($pos = strpos($route, '/')) !== false) {
            $id = substr($route, 0, $pos);

            if ($this->getRouter()->caseSensitive) {
                $id = strtolower($id);
            }
            $route=(string)substr($route, $pos + 1);

            if (!isset($basePath)) {
                if (($moduel = $owner->getModule($id)) !== null) {
                    return [
                        $this->createController($route, $module),
                        $this->parseActionParams($route)
                    ];
                }
                $basePath = $owner->getControllerPath();
            }

            $className = $this->projectNamespace . '\Controller\\' . ucfirst($id).'Controller';
            $classFile = $basePath . DIRECTORY_SEPARATOR . ucfirst($id).'Controller.php';

            if (is_file($classFile)) {
                if (!class_exists($className, false)) {
                    Z::loadFile($classFile);
                }

                if (class_exists($className)
                    && is_subclass_of($className, 'Z\Executors\ZController')) {
                    return [
                        new $className(),
                        $this->parseActionParams($route)
                    ];
                }
            }

            $basePath .= DIRECTORY_SEPARATOR . $id;
        }
    }

    public function parseActionParams($route)
    {
        if (($pos = strpos($route, '/')) !== false) {
               $actionId = substr($route, 0, $pos);
               return Z::app()->getRouter()->caseSensitive ? $actionId : strtolower($actionId);
        } else {
            return $route;
        }   
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
     * 获取 http request 组建
     * 
     * @return \Z\Request\ZWebRequset
     */
    public function getRequest()
    {
        return $this->getComponent('request');
    }
    /**
     * 注册系统核心组建
     * @return void
     */
    protected function registerCoreComponents()
    {
        $components = array(
            'router' => array(
                'class'     => 'Z\Router\ZWebRouter',
                'urlFormat' => 'path',
                'rules'     => [],
            ),
            'request' => array(
                'class'     => 'Z\Request\ZWebRequest',
                'enableXss' => true,
            ),
        );
        
        $this->setComponents($components);
    }
    public function getControllerPath()
    {
        if ($this->_controllerPath !== null) {
            return $this->_controllerPath;
        } else {
            return $this->_controllerPath = 
                $this->getBasePath() . DIRECTORY_SEPARATOR . 'Controller';
        }
    }

    /**
     * 初始化 web 
     * @return void
     */
    public function init()
    {
        $this->getRequest();
    }
}