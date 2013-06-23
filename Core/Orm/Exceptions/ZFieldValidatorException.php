<?php
/**
 * ZFieldValidatorException class
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

use Exception;
use Z\Core\Orm\ZFieldValidatorAbstract;

class ZFieldValidatorException extends Exception
{

    /**
     * field validator
     * @var \Z\Core\Orm\ZFieldValidatorAbstract
     */
    protected $validator;
    /**
     * 
     * @param \Z\Core\Orm\ZFieldValidatorAbstract $fieldValidator field validator
     * @param string                              $msg            error message
     * @param integer                             $code           error code
     */
    public function __construct(ZFieldValidatorAbstract $fieldValidator, $msg, $code = 0)
    {
        $this->validator = $fieldValidator;

        parent::__construct();
    }

    /**
     * 获得该异常的字段设置的Options值
     * @return array
     */
    public function getOptions()
    {
        return $this->validator->options;
    }
}
