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
    /**
     * 所有者(owner)的事件列表
     * @return array
     */
    public function events()
    {
        return array_merge(parent::events(), array(
            'onBeforeDispatch'=>'beforeDispatch',
            'onAfterDispatch' => 'afterDispatch',
        ));
    }

    /**
     * before dispatch 
     * @return void
     */
    public function beforeDispatch($event)
    {
        $context = $this->getOwner()->context;
        !$context->isDispatched && $this->bindParams($context);
    }

    /**
     * 在调用resource中的action之后要调用的方法
     * 
     * @return void
     */
    public function afterDispatch()
    {
        $context = $this->getOwner()->context;

        if (isset($context->cacheTime) && $context->cacheTime > 0) {
            $context->response->setExpires($context->cacheTime);
        }
        
    }

    /**
     * band action default parameters
     * @param  \Z\Executors\ZDispatchContext $dispatch dispatch
     * @return void
     */
    protected function bindParams(\ZDispatchContextInterface $context)
    {
        $params = $context->routeResult->arguments;

        if (empty($params)) {
            return;
        }

        $methodParams = $context->rfParams;

        $retParams = array();
        foreach ($methodParams as $methodParam) {
            $name = $methodParam->name;

            foreach ($params as $paramsKey => $paramsValue) {
                if ($paramsKey === $name) {
                    $retParams[$name] = $paramsValue;
                }

                if (!isset($params[$name])) {
                    if ($methodParam->isOptional()) {
                        $retParams[$name] = $methodParam->getDefaultValue();
                    } else {
                        $retParams[$name] = null;
                    }
                    
                }
            }
        }

        $context->params = $retParams;
    }
}