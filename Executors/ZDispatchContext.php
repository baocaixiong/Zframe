<?php
/**
 * ZDispatch Context
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
    ZDispatchContextInterface;

class ZDispatchContext implements ZDispatchContextInterface
{
    /**
     * request object
     * 
     * @var \Z\Request\ZRequestAbstract
     */
    public $request;

    /**
     * executor 
     * must a valid callback
     * @var array|mixed
     */
    public $executor;

    /**
     * will band inject parameters
     * @var array
     */
    public $routeResult = array();

    public $rfParams;

    public $methodName;
    /**
     * response object
     * @var \Z\Response\ZResponseAbstract
     */
    public $response;

    public $isDispatched = false;

    public $cacheTime = 0;

    public $etag = false;

    public $params = array();

    /**
     * 赋值操作
     * @param \Z\Request\ZRequestAbstract $request  request object
     * @param mixed                       $executor executor
     * @param string                      $actionId string
     * @param array                       $params   will band inject parameters
     */
    public function assignment(
        \ZRequestInterfase $request, \ZExecutorInterface $executor, $routeResult
    ) {
        $this->request = $request;
        $this->executor = $executor;
        $this->rfParams = $routeResult->route->params;
        $this->routeResult = $routeResult;
        $this->methodName = $routeResult->route->methodName;
        $this->cacheTime = $routeResult->cacheTime;
        $this->etag = $routeResult->etag;
        return $this;
    }

    /**
     * 初始化方法
     * 
     * @return void
     */
    public function initialize()
    {
        
    }
}