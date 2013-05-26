<?php
/**
 * Annotation 
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Annotation
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Annotation;


class ZClassAnnotation
{
    /**
     * class name
     * @var string
     */
    protected $className;
    
    /**
     * the class methods
     * @var array [\Z\Core\Annotations\ZMethodAnnotation, ...]
     */
    protected $methods;


    protected $methodUrls = array();

    protected $httpMethods = array();
    /**
     * comments of __construct
     * 
     * @param string $name className
     * 
     * @return return_value
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * class name 
     *
     * @return string
     */
    public function getName()
    {
        return $this->className;
    }

    /**
     * get the class methods
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * set methods
     * @param array $methods the class methods
     * @return void|null
     */
    public function setMethods($methods)
    {
        if (empty($methods) || !is_array($methods)) {
            return;
        }
        $this->methods = $methods;

        foreach ($methods as $method) {
            $this->methodUrls[$method->methodName] = $method->path;
            $this->httpMethods[$method->method] = true;
        }
    }

    /**
     * set method 
     * @param \Z\Core\Annotations\ZMethodAnnotation $method ZMethodAnnotation instance
     */
    public function setMethod($method)
    {
        if (empty($method) || !($method instanceof ZMethodAnnotation)) {
            return;
        }

        $this->methods[] = $method;
        $this->methodUrls[$method->methodName] = $method->path;
        $this->httpMethods[$method->method] = true;
    }
}