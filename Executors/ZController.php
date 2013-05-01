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
        $params = Z::app()->getRequest()->getParams();

        $callable = [$this, $actionId];

        if (is_callable($callable)) {
            $response = call_user_func_array($callable, $params);

            $response->respond();
        } else {
            throw new ZException(Z::t("This controller has not action \"{action}\".", ['{action}' => $actionId]));
        }
    }
}