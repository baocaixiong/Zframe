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

use Z\Z,
    Z\Core\ZAppComponent,
    \AnnotationInterface,
    Z\Exceptions\ZAnnotaionException;

class Annotation extends ZAppComponent 
{
    protected static $annotationCollection;

    /**
     * 收集器分隔符
     * @var string
     */
    public $separator = '.';

    /**
     * 要忽略扫描的目录
     * @var array
     */
    public $blacklist = ['.DS_Store', '.git', '.svn'];

    /**
     * 要扫描的目录
     * @var string
     */
    public $scanDir;

    /**
     * 缓存annotaion结果的目录
     * @var string
     */
    public $cacheDir;
    /**
     * 初始化方法
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $annotationCollection = $this->getAnnotationCollection();
        $annotationCollection->separator = $this->separator;
    }

    /**
     * get annotation collection 
     * @return \Z\Core\Annotation\AnnotationCollection
     */
    protected function getAnnotationCollection()
    {
        if (!is_null(self::$annotationCollection)) {
            return self::$annotationCollection;
        }

        return new AnnotationCollection();
    }

}