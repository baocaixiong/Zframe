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


class Annotation
{
    public $name;
    
    public $arguments = array();

    public $class = '';

    public $method = null;

    public $methodParameters = array();

    public $methodParametersCount = 0;

    /**
     * comments of __construct
     * 
     * @param string $name name
     * 
     * @return return_value
     * @author v.k <string@ec3s.com>
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
}