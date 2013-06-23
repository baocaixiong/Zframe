<?php
/**
 * ZValidatorAbstract class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\FieldValidators
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */

namespace Z\Core\FieldValidators;

use Z\Z;
use Z\Core\ZObject;
use Z\Exceptions\ZValidatorException;

abstract class ZValidatorAbstract extends ZObject
{
    /**
     * @var array list of built-in validators (name=>class)
     */
    public static $builtInValidators = array(
        'string'=>'Z\Core\Validator\ZStringValidator'
    );


    

    public function createValidator()
    {
        
    }
}