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

use Z\Z;
use Z\Helpers\ShowSystemPage;
use Z\Exceptions\ZInvalidConfigException;

abstract class ZApplication extends ZModule
{
    const EVENT_BEFORE_REQUEST =  'beforeRequest';

    const EVENT_AFTER_REQUEST = 'afterRequest';

    private $_config; //config array

    public $appName = 'My Application';

    public $charset = 'UTF-8';

    public $language = 'zh_cn';

    public $protectedNamespace = 'Project';

    public $modulesNamespace = 'Modules';

    /**
     * 应用ID
     * @var string
     */
    private $_id;

    private $_basePath;//应用目录
    private $_runtimePath; //程序运行目录

    /**
     * 是否catch所有的reque
     * <code>   
     * 'catchAllRequest' => [
     *     true,'<router>'
     * ];
     * </code>
     * @var boolean
     */
    public $catchAllRequest = array(
        false, ''
    );

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
            unset($config['basePath']);
        } else {
            $this->setBasePath('Protected');
        }
        
        Z::setPathOfNamespace($this->protectedNamespace, $this->getBasePath());
        Z::setPathOfNamespace($this->modulesNamespace, $this->getBasePath() . '/Modules');
        Z::setPathOfNamespace('Z', Z_PATH);

        $this->addDefaultEvent();
        $this->preinit();
        $this->initSystemHandlers();
        $this->registerCoreComponents(); //注册系统核心组件
        $this->attachBehaviors($this->behaviors);
        
        $this->setConfig($config);

        $this->init();
    }

    /**
     * run this application
     *
     * @return void
     */
    public function run()
    {
        if ($this->hasEventHandler(self::EVENT_BEFORE_REQUEST)) {
            $this->onBeginRequest(new ZEvent($this));
        }
        register_shutdown_function(array($this, 'end'), 0, false);
        $this->processRequest();
        
        if ($this->hasEventHandler(self::EVENT_AFTER_REQUEST)) {
            $this->onEndRequest(new ZEvent($this));
        }
    }

    /**
     * 应用ID
     * @return string
     */
    public function getId()
    {
        if ($this->_id!==null) {
            return $this->_id;
        } else {
            return $this->_id = sprintf('%x', crc32($this->getBasePath() . $this->appName));
        }
    }

    /**
     * 设置应用ID
     * @param string $value 应用ID
     */
    public function setId($value)
    {
        if (!is_string($value)) {
            throw new ZInvalidConfigException(Z::t('不正确的应用ID {id}', array('{id}' => $value)));
        }

        $this->_id = $value;
    }

    /**
     * 获得文件缓存组件
     * @return \Z\Caching\ZFileCache
     */
    public function getFileCache()
    {
        return $this->getComponent('fileCache');
    }

    /**
     * 获得数据库连接信息
     * @return \Z\Core\Orm\ZDbConnection
     */
    public function getDb()
    {
        return $this->getComponent('db');
    }

    /**
     * 获得 annotation 组件
     * 
     * @return \Z\Core\Annotation\Annotation
     */
    public function getAnnotation()
    {
        return $this->getComponent('annotation');
    }

    /**
     * 获得document parse 组件
     * 
     * @return \Z\Core\CoreComponents\ZParseComment
     */
    public function getParseComment()
    {
        return $this->getComponent('parseComment');
    }

    /**
     * get dispatch context component
     * @return \Z\Executors\ZDispatchContext
     */
    public function getDispatch()
    {
        return $this->getComponent('dispatch');
    }

    /**
     * 获取 http request 组建
     * 
     * @return \Z\Request\ZWebRequset
     */
    public function getRequest()
    {
        return $this->getComponent('request');
    }

    /**
     * db structure
     * @return \Z\Core\Orm\Structures\ZStructureInterface
     */
    public function getStructure()
    {
        return $this->getComponent('structure');
    }

    /**
     * 程序启动时的事件
     * @param \Z\Core\ZEvent $event 事件对象
     * @return void
     */
    public function onBeginRequest($event)
    {
        $this->fire(self::EVENT_BEFORE_REQUEST, $event);
    }

    /**
     * 程序结束时的事件
     * @param \Z\Core\ZEvent $event 事件对象
     * @return void
     */
    public function onEndRequest($event)
    {
        $this->fire(self::EVENT_AFTER_REQUEST, $event);
    }

    /**
     * 终结整个程序
     * @param  integer $status [description]
     * @param  boolean $exit   [description]
     * @return [type]          [description]
     */
    public function end($status=0, $exit=true)
    {
        if($this->hasEventHandler('onEndRequest'))
            $this->onEndRequest(new CEvent($this));
        if($exit)
            exit($status);
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
            Z::throwZException(Z::t('Error BasePath {basePath}', array('{basePath}' => $path)));
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
            register_shutdown_function(array($this, 'catchFatalError'));
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
        if (Z_DEBUG) {
            ShowSystemPage::showErrorandException($exception);
        }
    }

    /**
     * catchFatalError
     * @return void
     */
    public function catchFatalError()
    {
        $error = error_get_last();
        $ignore = E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_STRICT | E_DEPRECATED | E_USER_DEPRECATED;
        
        if (($error['type'] & $ignore) === 0) {
            $e = new \ErrorException(
                'Fatal Error: ' . $error['message'], 0, $error['type'], $error['file'], $error['line']
            );
            $this->handleException($e);
        }
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
            $prefix . ' : ' . $errstr, 0, $errno, $errfile, $errline
        );
        //$this->handleException($e);
    }
    
    /**
     * 系统核心commponents
     * @return array
     */
    protected function coreComponents ()
    {
        return array(
            'annotation' => array(
                'class' => 'Z\Core\Annotation\AnnotationManager',
                'separator' => '.',
            ),
            'parseComment' => array(
                'class' => 'Z\Core\CoreComponents\ZParseComment',
            ),
            'dispatch' => array(
                'class' => 'Z\Executors\ZDispatchContext',
            ),
            'request' => array(
                'class'     => 'Z\Request\ZWebRequest',
                'enableCsrf' => true,
            ),
            'db' => array(
                'class' => 'Z\Core\Orm\ZDbConnection',
            ),
            'fileCache' => array(
                'class' => 'Z\Caching\ZFileCache',
            )
        );
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
    abstract public function processRequest();
}