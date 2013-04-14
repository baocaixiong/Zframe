<?php
/**
 * zphp framework base class
 *
 * PHP Version 5.4
 *
 * @className Z
 * @category  System
 * @package   System
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z;

/**
 * get application start timestamp
 */
defined('Z_BEGIN_TIME') or define('Z_BEGIN_TIME', microtime(true));

/**
 * set app debug 
 */
defined('Z_DEBUG') or define('Z_DEBUG', true);
/**
 * set framework path
 */
defined('Z_PATH') or define('Z_PATH', dirname(__FILE__));


defined('Z_EXCEPTION_HANDLER_DEBUG') or define('Z_EXCEPTION_HANDLER_DEBUG', true);
defined('Z_ERROR_HANDLER_DEBUG') or define('Z_ERROR_HANDLER_DEBUG', true);

defined('NS_SEPARATOR') or define('NS_SEPARATOR', '\\');
defined('PHP_EXT') or define('PHP_EXT', '.php');

/**
 * require singleton file trait
 */
require_once Z_PATH . '/Helpers/ZSingleton.php';
/**
 * Z Framework base class 
 *
 * @author  baocaixiong <baocaixiong@gmail.com>
 * @package System
 * @since   v0.1
 */
class Z
{
    const Z_VERSION = 'v0.1';
    private static $_app; 
    
    private static $_namespaceMapper = [];

    private static $_imports = [];

    private static $_language = [];

    private static $_loadedFile = [];

    /**
     * get z framework version 
     * @return String framework version
     */
    public static function getZVersion()
    {
        return self::Z_VERSION;
    }

    /**
     * create a web application
     * @param  Array $config config array
     * @return ZWebApplication
     */
    public static function createWebApplication($config = null)
    {
        return self::createApplication('Z\Applications\ZWebApplication', $config);
    }

    /**
     * Returns the application singleton or null if the singleton has not been created yet.
     * @return \Z\Core\ZApplication 
     */
    public function app ()
    {
        return self::$_app;
    }
    /**
     * create application 
     * @param  String $className must a application className
     * @param  Array $config    config array
     * @return mixed the application instance
     */
    public static function createApplication($className, $config = null)
    {
        return new $className($config);
    }

    /**
     * autoload method
     * @param  String $className class name
     * @return Void
     */
    public static function autoload($className)
    {
        if (isset(self::$_coreClasses[$className])) {
            include Z_PATH . self::$_coreClasses[$className];
        } elseif (($pos = strpos($className, NS_SEPARATOR)) !== FALSE) {
            $rootAliasName = substr($className, 0, $pos);
            $rootaliasPath = self::getPathOfNamespace($rootAliasName);
            require (
                $rootaliasPath . DIRECTORY_SEPARATOR . str_replace(
                    NS_SEPARATOR, DIRECTORY_SEPARATOR, substr($className, $pos + 1)
                ) . PHP_EXT);

        }
    }
    /**
     * set this application object
     * @param $application 
     */
    public static function setApplication($application)
    {
        if (self::$_app === null || $application === null) {
            self::$_app=$application;
        } else {
            self::throwZException(Z::t('The application has been created', []));
        }
    }

    /**
     * 翻译
     * @param String $message  要翻译的文字  使用{}包起来的是翻译参数
     * @param Array  $params   翻译文字中的参数
     * @param String $category 使用的翻译包 z 和 用户定义 
     * 
     * @return String 已经翻译好的结果
     */
    public static function t($message, $params = array(), $category = 'z')
    {
        $languageDir = ($category === 'z' ? Z_PATH : self::$_app->getBasePath()) . '/Language';
        
        if (isset(self::$_language[$message])) {
            $message = self::$_language[$message];
        }
        $languages = self::loadFile(
            $languageDir . '/' . self::$_app->language . '/' . $category  . PHP_EXT
        );
        if (isset($languages[$message])) {
            $message = $languages[$message];
        }
        self::$_language[$message] = $message;
        return $params!==array() ? strtr($message, $params) : $message;
    }

    /**
     * throw ZException 
     * @param  String $message exception content
     * @param  int    $code    exception code
     * @return Void
     * @throw ZException
     */
    public static function throwZException($message, $code = 0)
    {
        throw new \Z\Exceptions\ZException($message, $code);
    }

    /**
     * 设置一个名字空间的路径
     * @param  String $name 名字空间的名称
     * @param  String $path 要设定的路径
     * @return Void
     */
    public static function setPathOfNamespace($name, $path)
    {
        if (empty($path)) {
            unset(self::$_namespaceMapper[$name]);
        } else {
            self::$_namespaceMapper[$name] = $path;
        }
    }

    /**
     * 创建一个组建对象
     * @return \Z\Core\ZCore 刚刚创建的组件对象
     */
    public static function createComponent($config)
    {
        if (is_string($config)) {
            $type = $config;
            $config = array();
        } elseif (isset($config['class'])) {
            $type = $config['class'];
            unsset($config['class']);
        } else {
            Z::throwZException(
                Z::t('Object configuration must be an array containing a "class" element')
            );
        }
        if (!class_exists($type, false)) {
            $type = Z::import($type, true);
        }

        if (($num = func_num_args()) > 1) {
            $args=func_get_args();
            if (2 === $n) {
                $object = new $type($args[1]);
            } elseif(3 === $n) {
                $object = new $type($args[1], $args[2]);
            } elseif(4 === $n) {
                $object = new $type($args[1], $args[2], $args[3]);
            } else {
                unset($args[0]);
                $class = new ReflectionClass($type);
                $object = call_user_func_array(array($class, 'newInstance'), $args);
            }
        } else {
            $object = new $type();
        }
        foreach ($config as $key => $value) {
            $object->$key = $value;
        }
        return $object;
    }

    /**
     * get Path of namespace
     * @return String 转换好的路径
     */
    public static function getPathOfNamespace($name)
    {
        if (isset(self::$_namespaceMapper[$name])) {
            return self::$_namespaceMapper[$name];
        } elseif (($pos = strpos($name, NS_SEPARATOR) !== FALSE)) {
            $rootAliasName = substr($name, 0, $pos);
            if (isset(self::$_namespaceMapper[$rootAliasName])) {
                return self::$_namespaceMapper[$name] = rtrim(
                    self::$_namespaceMapper[$rootAliasName] . DIRECTORY_SEPARATOR . str_replace(
                        NS_SEPARATOR, DIRECTORY_SEPARATOR, substr($name, $pos + 1)
                    ),
                    DIRECTORY_SEPARATOR
                );
            }
        }
        return null;
    }

    /**
     * import class file (根据类的名字空间)
     * @param String $className 要载入的类名字空间
     * @return String className
     */
    public static function import($alias, $forceInclude = false)
    {
        if (isset(self::$_imports[$alias])) {
            return self::$_imports[$alias];
        } elseif (($pos = strpos($alias, NS_SEPARATOR)) !== false) {
            if (!!$path = self::getPathOfNamespace($alias)) {
                if (is_file($path . PHP_EXT)) {
                    include self::getPathOfNamespace($alias) . PHP_EXT;
                    return self::$_imports[$alias] = $alias;
                }
            } else {
                self::throwZException(Z::t('import alias {alias} error', ['{alias}' => $alias]));
            }
        }
        self::throwZException(Z::t('import alias {alias} error', ['{alias}' => $alias]));
    }

    /**
     * load file 
     * @param String $fileName [description]
     * @return Mixed 成功 true,否则 false
     */
    public static function loadFile($fileName)
    {
        if (empty($fileName)) {
            return false;
        }
        if (isset(self::$_loadedFile[$fileName])) {
            return self::$_loadedFile[$fileName];
        } else {
            $content = include $fileName;
            return self::$_loadedFile[$fileName] = $content;
        }
    }
    /**
     * framework system class 
     * @var Array
     */
    private static $_coreClasses=array(
        'Z\Core\ZApplication' => '/Core/ZApplication.php',
        'Z\Applications\ZWebApplication' => '/Applications/ZWebApplication.php',
        'Z\Exceptions\ZException' => '/Exceptions/ZException.php',
        'Z\Core\ZCore' => '/Core/Zcore.php',
        'Z\Core\ZModule' => '/Core/ZModule.php',
        'Z\Core\ZCore' => '/Core/ZCore.php',
        'Z\Core\ZEvent' => '/Core/ZEvent.php',
        'Z\Helpers\ZSingleton' => '/Helpers/ZSingleton.php',
    );
}//end of Z class 

spl_autoload_register(['Z\Z', 'autoload']);
require(Z_PATH . '/Core/interfaces.php');