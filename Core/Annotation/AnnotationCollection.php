<?php
/**
 * Annotation collection 
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

use Z\Collections\ZMap,
    \AnnotationCollectionInterface;

class AnnotationCollection extends ZMap implements AnnotationCollectionInterface
{

    public $separator = '.';

    private static $_annotation;

    /**
     * set separator 
     * @param  string $value separator
     * @return void
     */
    public function setSeparator($value)
    {
        if (!empty($value) && is_string($value)) {
            $this->separator = $value;
        }
    }

    /**
     * get annotation from name 
     * @param String $name    想要获得配置的名字 可以是 以 $this->separator 隔开的参数
     * @param Mixed  $default 如果配置不存在，会返回的默认值
     * 
     * @return Mixed 返回数据
     */
    public function getAnnotation($name = null, $default = null)
    {
        if ($name === null) {
            return self::$_annotation;
        } elseif (strpos($name, $this->separator) === false) {
            return array_key_exists($name, self::$_annotation)
                ? self::$_annotation[$name] : $default;
        } else {
            $annotationPars = explode($this->separator, $name);
            $config = &self::$_annotation;
            foreach ($annotationPars as $annotationPar) {
                if (!isset($pos[$annotationPar])) {
                    return $default;
                }
                $config = &$config[$annotationPar];
            }
            return $config;
        }
    }

    /**
     * set annotation value 
     * 
     * @param string | Array $name  要设置的annotation
     *     可以使用 $this->separator 分开，这样会作为数组的子
     * @param string | Array $value 值
     * 
     * @return \Z\Core\Annotation\AnnotationCollection
     */
    public function setAnnotation($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->setConfig($key, $value);
            }
            return $this;
        }

        if (strpos($name, $this->separator) === false) {
            self::$_annotation[$name] = $value;
            return $this;
        } else {
            $annotationPars = explode($this->separator, $name);
            $config = &self::$_annotation;

            $depth = count($annotationPars) - 1;

            for ($i = 0; $i <= $depth; $i++) {
                $annotationPar = $annotationPars[$i];
                if ($i < $depth) {
                    if (!isset($config[$annotationPar])) {
                        $config[$annotationPar] = array();
                    }
                    $config = &$config[$annotationPar];
                } else {
                    $config[$annotationPar] = $value;
                }
            }
        }
        return $this;
    }
}