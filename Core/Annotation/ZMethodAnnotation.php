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


class ZMethodAnnotation
{
    /**
     * method name
     * @var string
     */
    public $methodName;

    public $params;

    public $className;

    public function __construct($className, $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;
    }

    /**
     * get method name
     * @return string the method name
     */
    public function getName()
    {
        return $this->methodName;
    }
}