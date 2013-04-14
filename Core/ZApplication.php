<?php
/**
 * Z Application class 
 * 此类是全局应用app
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core;

use \Z\Z,
    \Z\Helpers\ShowSystemPage;

abstract class ZApplication extends ZModule
{
    private $_config; //config array

    public $appName = 'My Application';

    public $charset = 'UTF-8';

    public $language = 'zh_cn';

    private $_basePath;//应用目录
    private $_runtimePath; //程序运行目录

    /**
     * run application construct method
     * 
     * @param Mixed $config the application instance
     * 
     * @return Mixed sub instance
     */
    public function __construct($config = null)
    {
        Z::setApplication($this);
        if (is_string($config)) {
            require($config);
        }
        
        if ($config['basePath']) {
            $this->setBasePath($config['basePath']);
        } else {
            $this->setBasePath('Protected');
        }
        Z::setPathOfNamespace('WebRoot', dirname($_SERVER['SCRIPT_FILENAME']));
        Z::setPathOfNamespace('Z', Z_PATH);
        $this->preinit();
        $this->initSystemHandlers();
        //$this->registerCoreComponents(); //注册系统核心组件
        
        $this->setConfig($config);

    }

    /**
     * run this application
     *
     * @return void
     */
    public function run()
    {
        throw new \Z\Exceptions\ZHttpException("Error Processing Request", 1);
        
        //Z::throwZException('haha');
var_dump(asdf);
        $this->notify();
    }

    /**
     * get BasePath
     * 
     * @return String 程序运行目录
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }
    /**
     * set base path
     * 
     * @param String $path path
     *
     * @return void
     * @throw \Z\Exceptions\ZException
     */
    public function setBasePath($path)
    {
        if (($this->_basePath = realpath($path)) === false || !is_dir($this->_basePath)) {
            Z::throwZException(Z::t('Error BasePath {basePath}', ['{basePath}' => $path]));
        }
    }
    /**
     * initialize system exception handler and error handler
     *  
     * @return void
     */
    public function initSystemHandlers()
    {
        if (Z_EXCEPTION_HANDLER_DEBUG) {
            set_exception_handler(array($this, 'handleException'));
        }
        if (Z_ERROR_HANDLER_DEBUG) {
            set_error_handler(array($this, 'handleError'), error_reporting());
        }
    }

    /**
     * 捕获没有catch 的异常
     * @param \Exception $ex 没有捕获的异常对象
     * @return void
     */
    public function handleException(\Exception $exception)
    {
        restore_error_handler();
        restore_exception_handler();
        ShowSystemPage::showErrorandException($exception);
    }

    /**
     * 捕获 PHP执行错误处理如warnings，notices
     * @return [type] [description]
     */
    public function handleError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        restore_error_handler();
        //restore_exception_handler();
        $arrPrefix = array (
               E_ERROR             => 'ERROR',
               E_WARNING           => 'WARNING',
               E_PARSE             => 'PARSING ERROR',
               E_NOTICE            => 'NOTICE',
               E_CORE_ERROR        => 'CORE ERROR',
               E_CORE_WARNING      => 'CORE WARNING',
               E_COMPILE_ERROR     => 'COMPILE ERROR',
               E_COMPILE_WARNING   => 'COMPILE WARNING',
               E_USER_ERROR        => 'USER ERROR',
               E_USER_WARNING      => 'USER WARNING',
               E_USER_NOTICE       => 'USER NOTICE',
               E_STRICT            => 'STRICT NOTICE',
               E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR'
        );
        $prefix = isset($arrPrefix[$errno]) ? $arrPrefix[$errno] : 'ERROR';
        throw new \ErrorException(
            $prefix . ' : ' . $errstr, 0, $errno, $errfile, $errline);
        
    }

    
    /**
     * prepare initialize
     * 
     * @return void
     */
    public function preinit()
    {
        //预处理操作，等待重载
    }
    /**
     * abstract method parse request
     */
    abstract public function parseRequest();
}