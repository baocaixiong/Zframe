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
    public function __construct(\ZRequestInterfase $request, ZApplication $application)
    {
        $this->request = $request;
        $this->application = $application;
    }

    public function executor($actionId)
    {
        $response = $this->execute($actionId);
    }
}