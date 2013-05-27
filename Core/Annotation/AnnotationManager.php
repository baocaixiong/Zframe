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
    AnnotationInterface,
    Z\Exceptions\ZAnnotationException,
    Z\Collections\ZMap,
    Z\Router\ZRoute;

class AnnotationManager extends ZAppComponent implements AnnotationInterface
{
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
     * 缓存文件名称
     * @var string
     */
    public $cacheName = 'annotation.cache';

    private $_collection;

    /**
     * 初始化方法
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (empty($this->scanDir)) {
            $this->scanDir = Z::app()->getBasePath();
        }

        if (empty($this->cacheDir)) {
            $this->cacheDir = Z::app()->getBasePath() . '/runtime/cache';
        }

        $this->checkScanDirAndCacheDir();//检查目录
    }

    /**
     * 收集
     * @return \Z\Annotation\ZAnnotationCollection
     */
    private function _collect($scanDir = '')
    {
        $this->_collection = new ZAnnotationCollection();

        if (empty($scanDir)) {
            $scanDir = $this->scanDir;
        }
        $files = $this->getAllScriptFiles($scanDir);

        $paramComment = $this->getParseComment();

        foreach ($files as $file) {

            if ($this->isScandedFile($file)) {
                continue;
            }

            $classes = $this->findClassFromFile($file);

            foreach ($classes as $class) {
                $rfClass = new \ReflectionClass($class);
                $classAnnotation = $this->createClassAnnotation($rfClass->getName());
               
                foreach ($paramComment->parse($rfClass->getdoccomment()) as $key => $meta) {
                    $classAnnotation->{$key} = $meta;
                }

                foreach ($this->getOwnMethods($rfClass, \ReflectionMethod::IS_PUBLIC) as $rfMethod) {
                    $methodAnnotation = $this->createMethodAnnotation($rfMethod->class, $rfMethod->getName());
                    foreach ($paramComment->parse($rfMethod->getdoccomment()) as $key => $meta) {
                        $methodAnnotation->{$key} = $meta;
                    }

                    $methodAnnotation->params = $rfMethod->getParameters();
                    $classAnnotation->setMethod($methodAnnotation);
                }

                $this->_collection->add($classAnnotation->getName(), $classAnnotation);
            }
        }
    }

    /**
     * get annotations
     * 
     * @return \Z\Core\Annotation\ZAnnotationCollection
     */
    public function getAnnotations()
    {
        if (is_null($this->_collection)) {
            $this->_collect();
        }

        return $this->_collection;
    }

    /**
     * get Annotation object
     * @return \Z\Core\Annotation\ZClassAnnotation
     */
    public function createClassAnnotation($name)
    {
        return new ZClassAnnotation($name);
    }

    /**
     * create a methodAnnotation instance
     * @param  string $className method's class name
     * @param  string $name      method name
     * @return \Z\Core\Annotation\ZMethodAnnotation
     */
    public function createMethodAnnotation($className, $name)
    {
        return new ZMethodAnnotation($className, $name);
    }

    /**
     * 从文件中获得class name
     * @param  string  $file      filename
     * @param  boolean $filtrateZ whether filtrate ZFrame class
     * @return array
     */
    protected function findClassFromFile($file, $filtrateZ = true)
    {
        $declaredClasses = get_declared_classes();//获得已经加载了的class
        include $file;

        $newClasses = array_diff(get_declared_classes(), $declaredClasses);

        $result = array();

        if ($filtrateZ) {
            foreach ($newClasses as $class) {
                if (strncasecmp($class, 'Z', 1) === 0) {
                    continue;
                }
                $result[] = $class;
            }
        } else {
            $result = $newClasses;
        }
        
        return $result;
    }

    /**
     * 获得document parse 组件
     * 
     * @return \Z\Core\CoreComponents\ZParseComment
     */
    protected function getParseComment()
    {
        $parse = Z::app()->getParseComment();
        return $parse;
    }

    /**
     * 扫描项目下面所有的php类文件
     * 
     * @param  string $dir 要扫描的目录
     * @return array 扫描得出的文件
     */
    protected function getAllScriptFiles($dir)
    {
        $files = array();
        $dirIterator = new \DirectoryIterator($dir);

        foreach ($dirIterator as $file) {
            if ($file->isDot()) {
                continue;
            }

            $fileName = $file->getFileName();

            //如果在忽略列表，直接continue
            if (in_array($fileName, $this->blacklist)) {
                continue;
            }

            /**
             * ###在此规定
             * * 如果项目中的文件是包含一个可引用的类：目录必须同名字空间
             * * 如果项目中一个目录下没有可用的类，例如配置文件或者是缓存文件，
             *   请使用小写的目录名称和小写的文件名称
             */
            if (!preg_match('@[A-Z].*@', $fileName)) {
                continue;
            }

            $filePath = $file->getRealPath();

            if ($file->isDir()) {
                $files = array_merge($files, $this->getAllScriptFiles($filePath));
            } elseif ($file->getExtension() === 'php') {
                $files[] = $filePath;
            }
        }

        return $files;
    }

    /**
     * is scanded dir 
     * @param  string  $fileName [description]
     * @return boolean           [description]
     */
    protected function isScandedFile($fileName)
    {
        foreach (get_included_files() as $incluededFile) {

            if (strtolower($fileName) == strtolower($incluededFile)) {
                return true;
            }

        }

        return false;
    }

    /**
     * check scanDir and cacheDir status
     * 
     * @return void
     * @throws ZAnnotationException 
     */
    protected function checkScanDirAndCacheDir()
    {
        if (!is_dir($this->scanDir)) {
            throw new ZAnnotationException(Z::t('{scanDir} is not existing', array('{scanDir}' => $this->scanDir)));
        } else {
            if (!is_writeable($this->scanDir)) {
                throw new ZAnnotationException(Z::t('{scanDir} is not writeable', array('{scanDir}' => $this->scanDir)));
            }
        }

        if (!is_dir($this->cacheDir)) {
            throw new ZAnnotationException(Z::t('{cacheDir} is not existing', array('{cacheDir}' => $this->cacheDir)));
        } else {
            if (!is_writeable($this->cacheDir)) {
                throw new ZAnnotationException(Z::t('{cacheDir} is not writeable', array('{cacheDir}' => $this->cacheDir)));
            }
        }
    }
}