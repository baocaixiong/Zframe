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
        ));
    }

    /**
     * before dispatch 
     * @return void
     */
    public function beforeDispatch($event)
    {
        $this->bindParams($this->getOwner()->dispatch);
    }

    /**
     * band action default parameters
     * @param  \Z\Executors\ZDispatchContext $dispatch dispatch
     * @return void
     */
    protected function bindParams(\ZDispatchContextInterface $dispatch)
    {
        $params = $dispatch->request->getParams();
        if (empty($params)) {
            return;
        }

        $rfMethod = new \ReflectionMethod(get_class($this->getOwner()), $dispatch->actionId);

        $methodParams = $rfMethod->getParameters();

        $retParams = [];
        foreach ($methodParams as $methodParam) {
            $name = $methodParam->name;

            foreach ($params as $paramsKey => $paramsValue) {
                if ($paramsKey == $name) {
                    $retParams[$name] = $paramsValue;
                }

                if (!isset($params[$name]) && $methodParam->isOptional()) {
                    $retParams[$name] = $methodParam->getDefaultValue();
                }
            }
        }

        $dispatch->params = $retParams;
    }
}