<?php
/**
 * ZRestfulRouter class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Router
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Router;

use Z\Z;
use Z\Exceptions\ZAnnotationException;

class ZRoute
{
    public $class;

    public $function;

    public $methods;

    public $path;

    public $etag;

    /**
     * 构造方法，初始化整个route
     * 
     * @param string       $class    class name
     * @param string       $function action function name
     * @param string|array $methods  http method
     * @param string       $path     http path
     *
     * @return \Z\Router\ZRoute
     */
    public function __construct($class, $function, $methods, $path)
    {
        $this->class = $class;
        $this->function = $function;
        if (is_array($methods)) {
            $this->methods = $methods;
        } else {
            $this->methods[] = $methods;
        }
        
        $this->path = $path;
    }

    /**
     * MAGIC METHOD
     * This static method is called for classes exported by var_export()
     * 
     * @return void
     */
    public static function __set_state()
    {
        $route = new Route($array['class'], $array['function'], $array['methods'], $array['path']);
        return $route;
    }

    /**
     * MAGIC METHOD
     * 
     * @param string $name  name
     * @param mixed  $value value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        throw new ZAnnotationException(
            Z::t('route has not this {property}.', array('{property}' => $name))
        );
    }
}

