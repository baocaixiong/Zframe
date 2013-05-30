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
    Z\Core\ZCore,
    Z\Core\ZEvent;

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

    public $context;

    /**
     * 构造方法，初始化整个executer
     * @param ZApplication       $application [description]
     */
    public function __construct(ZApplication $application)
    {
        $this->application = $application;
    }

    /**
     * 初始化
     * 
     * @return void
     */
    public function init(\ZDispatchContextInterface $context)
    {
        $this->context = $context;
        $this->request = $context->request;
        $this->attachBehaviors($this->behaviors());
    }

    /**
     * 执行 action
     * @param  string $actionId action id
     * @return void
     */
    public function executor()
    {
        $this->onBeforeDispatch(new ZEvent($this));
        $this->execute($this->context);
        $this->onAfterDispatch(new ZEvent($this));
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
    
    /**
     * before dispatch event
     * @param  \Z\Core\ZEvent $event event instance
     * @return void
     */
    public function onBeforeDispatch($event)
    {
        $this->raiseEvent('onBeforeDispatch', $event);
    }

    /**
     * after dispatch 
     * @param  [type] $event [description]
     * @return [type]        [description]
     */
    public function onAfterDispatch($event)
    {
        $this->raiseEvent('onAfterDispatch', $event);
    }
}