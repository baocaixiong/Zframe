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
    public function execute(\ZDispatchContextInterface $context)
    {
        $callable = array($this, $context->methodName);

        if (is_callable($callable)) {
            /**
             * http cache
             */
            // if ($request->checkClientCacheIsValid()) {
            //     Z::app()->getHttpResponse()->notModified();
            //     $this->responed(Z::app()->getHttpResponse());
            //     return ;
            // }

            $response = call_user_func_array($callable, $context->params);

            $context->response = $response;
        } else {
            throw new ZException(Z::t("This controller has not action \"{action}\".", array('{action}' => $actionId)));
        }   
    }

    /**
     * Respond to client
     * @param  \Z\Resposne\ZReponseAbstract $resposne response instance
     * @return void
     */
    protected function responed (\ZResponseInterface $resposne)
    {
        if (null == $resposne) {
            return;
        }
        
        foreach ($resposne->getAllheaders() as $str => $replace) {
            header($str, $replace);
        }

        echo $resposne->getBody();
    }
}