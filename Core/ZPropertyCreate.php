<?php
/**
 * ZProperty class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core;

use Z\Z;
use Z\Core\Annotation\ZClassAnnotation;

class ZPropertyCreate extends ZObject
{
    /**
     * 单个类的所有定义在Annotation里面的属性
     * @var array
     */
    private $_properties = array();

    /**
     * 对于单个类中，已经使用的Property，会被缓存在values里面
     * @var array
     */
    private $_values = array();

    /**
     * 本类的Annotation
     * @var \Z\Core\Annotation\ZClassAnnotation
     */
    private $_annotations;

    /**
     * CONSTRUCT METHOD
     * @param  \Z\Core\Annotation\ZClassAnnotation $classAnnotation this class annotation
     * @return \Z\Core\ZProperty 
     */
    public function __construct(ZClassAnnotation $classAnnotation)
    {
        $this->_annotations = $classAnnotation;
        $this->_parseProperties();
    }

    /**
     * 获取一个Property
     * @param  string $name property name
     * @return mixed
     */
    public function get($name)
    {
        
    }

    /**
     * 解析Properties
     * @return void
     */
    private function _parseProperties()
    {
        $annotations = $this->_annotations;
//var_dump($annotations);
        foreach ($annotations as $key => $value) {
            var_dump($key, $value);
        }
    }
}