<?php
/**
 * Z Exception class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Exceptions
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Exceptions;
/**
 * ZException
 *
 * @author  baocaixiong <baocaixiong@gmail.com>
 * @since   v0.1
 */
class ZException extends \Exception
{
	public function setMessage($message)
    {
        $this->message = $message;
    }
}