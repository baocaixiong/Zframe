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

class ZController extends ZExecutor
{
    public $defaultAction = 'index';

    public function __get ($name)
    {
        if ($name = 'httpResponse') {
            return $this->application->getHttpResponse();
        } else {
            return parent::__get($name);
        }
    }

    /**
     * 预处理方法
     * @return [type] [description]
     */
    public function init ()
    {
        
    }

    public function execute($actionId)
    {
        if (empty($actionId)) {
            $actionId = $this->defaultAction;
        }

        $request = Z::app()->getRequest();
        $params = $request->getParams();

        $callable = [$this, $actionId];

        if (is_callable($callable)) {
            if ($request->checkClientCacheIsValid()) {
                Z::app()->getHttpResponse()->notModified();
                $this->responed(Z::app()->getHttpResponse());
                return ;
            }

            $response = call_user_func_array($callable, $params);

            $this->responed($response);
        } else {
            throw new ZException(Z::t("This controller has not action \"{action}\".", ['{action}' => $actionId]));
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