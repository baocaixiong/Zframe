<?php
/**
 * ZBaseRequest class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Request
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Request;

use \Z\Z,
    \Z\Core\ZAppComponent;

/**
 * ZBaseRequest class
 */
abstract class ZBaseRequest extends ZAppComponent implements \ZRequestInterfase
{
    abstract public function getMethod();
    abstract public function getRawBody();
    abstract public function getContentType();
}