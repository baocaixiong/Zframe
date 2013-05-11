<?php
/**
 * Z Base Executor  class
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
    Z\Core\ZApplication,
    Z\Core\ZCore;

abstract class ZExecutor extends ZCore implements \ZExecutorInterface
{
    /**
     * Current Request
     * 
     * @var ZRequestInterfase $request request
     */
    public $request;

    /**
     * Current Application
     * @var \Z\Core\ZApplication $application application
     */
    public $application;

    /**
     * 构造方法，初始化整个executer
     * @param \ZRequestInterfase $request     [description]
     * @param ZApplication       $application [description]
     */
    public function __construct(\ZRequestInterfase $request, ZApplication $application)
    {
        $this->request = $request;
        $this->application = $application;
    }

    public function init()
    {
        $this->attachBehaviors($this->behaviors());
    }

    /**
     * 执行 action
     * @param  string $actionId action id
     * @return void
     */
    public function executor(\ZDispatchContextInterface $dispath)
    {
        $response = $this->execute($dispath);
    }

    /**
     * 行为列表
     * @return array
     */
    protected function behaviors()
    {
        return array(
            'dispatch' => array(
                'class' => 'Z\Executors\ZExecutorBehavior',
            ),
        );
    }

    protected function bindParams()
    {

    }
}