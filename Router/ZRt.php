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

class ZRt {
    public $className; // Class
    public $methodName; // Function
    
    function __construct($route) {
        $this->className = $route->className;
        $this->methodName = $route->methodName;
    }
    
    function toRoute() {
        $route = new ZRoute($this->className, $this->methodName, array(), '');
        return $route;
    }
}
?>