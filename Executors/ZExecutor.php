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
    const EVENT_BEFORE_DISPATCH = 'onBeforeDispatch';
    const EVENT_AFTER_DISPATCH = 'onAfterDispatch';
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
     * http request context
     * @var \Z\Executors\ZDispatchContext
     */
    public $context;

    /**
     * web response 
     * @var \Z\Response\ZHttpResponse
     */
    public $response;

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
        $this->beforeDispatch(new ZEvent($this));
        $this->response = $this->context->response;
        $this->execute($this->context);
        $this->afterDispatch(new ZEvent($this));
        $this->responed($this->response);
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
     * Respond to client
     * @param  \Z\Resposne\ZReponseAbstract $resposne response instance
     * @return void
     */
    protected function responed (\ZResponseInterface $response)
    {
        $content = ob_get_clean();

        if (null == $response) {
            return;
        }
        
        foreach ($response->getAllheaders() as $str => $replace) {
            header($str, $replace);
        }
        echo $content;
        echo $response->getBody();
    }

    /**
     * before dispatch event
     * @param  \Z\Core\ZEvent $event event instance
     * @return void
     */
    public function beforeDispatch($event)
    {
        $this->fire(self::EVENT_BEFORE_DISPATCH, $event);
    }

    /**
     * after dispatch 
     * @param  [type] $event [description]
     * @return [type]        [description]
     */
    public function afterDispatch($event)
    {
        $this->fire(self::EVENT_AFTER_DISPATCH, $event);
    }
}