<?php
/**
 * Z DbException class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Z\Core\Orm\Exceptions
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Exceptions;

use Z\Exceptions\ZException;
/**
 * ZException
 *
 * @author  baocaixiong <baocaixiong@gmail.com>
 * @since   v0.1
 */
class ZDbException extends ZException
{
    /**
     * 通常是pdo::errorInfo
     * @var mixed
     */
    public $errorInfo;

    public function __construct($message, $code = 0, $errorInfo = null)
    {
        $this->errorInfo = $errorInfo;
        parent::__construct($message, $code);
    }
}