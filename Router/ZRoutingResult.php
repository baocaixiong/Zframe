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


class ZRoutingResult {
	public $route = null;
	public $arguments = array();
	public $routeExists = false;
	public $methodIsSupported = false;
	public $acceptableMethods = array();
}

?>