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
     * exector action name
     * @var string
     */
    public $actionId;

    /**
     * will band inject parameters
     * @var array
     */
    public $params = [];

    /**
     * response object
     * @var \Z\Response\ZResponseAbstract
     */
    public $response;

    /**
     * 赋值操作
     * @param \Z\Request\ZRequestAbstract $request  request object
     * @param mixed                       $executor executor
     * @param string                      $actionId string
     * @param array                       $params   will band inject parameters
     */
    public function assignment(
        \ZRequestInterfase $request, \ZExecutorInterface $executor, $actionId
    ) {
        $this->request = $request;
        $this->executor = $executor;
        $this->actionId = $actionId;
        $this->params = $request->getParams();

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