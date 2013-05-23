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
    public $className;
    
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
}