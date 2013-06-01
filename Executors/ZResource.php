<?php
/**
 * Z mvc controller class
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
    Z\Exceptions\ZException;

class ZResource extends ZExecutor
{
    /**
     * execut this action
     * 
     * @param  \ZDispatchContextInterface $context Dispatch context
     * @return null|void
     */
    public function execute(\ZDispatchContextInterface $context)
    {
        if ($context->isDispatched) {
            return null;
        }
        $callable = array($this, $context->methodName);

        /**
         * http cache
         */
        // if ($request->checkClientCacheIsValid()) {
        //     Z::app()->getHttpResponse()->notModified();
        //     $this->responed(Z::app()->getHttpResponse());
        //     return ;
        // }

        call_user_func_array($callable, $context->params);
    }
}