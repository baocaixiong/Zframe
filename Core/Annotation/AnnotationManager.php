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
     * controller or resource path annotation
     * @var string
     */
    public $exectorAnnotation = 'root';

    public $exectorActionAnnotation = 'http';

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

    public $annotationClass = 'Z\Core\Annotation\ZClassAnnotation';

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
     * @return \Z\Collections\ZMap
     */
    public function collect($scanDir = '')
    {
        $collection = new ZMap();

        if (empty($scanDir)) {
            $scanDir = $this->scanDir;
        }
        $files = $this->getAllScriptFiles($scanDir);

        $paramComment = $this->getParseComment();

        foreach ($files as $file) {

            $classes = $this->findClassFromFile($file);

            foreach ($classes as $class) {
                $rfClass = new \ReflectionClass($class);
                $classAnnotation = $this->getClassAnnotation($rfClass->getName());
                foreach ($paramComment->parse($rfClass->getdoccomment()) as $meta) {
                    $classAnnotation->{$meta[0]} = $meta[1][0];
                }

                if (!isset($classAnnotation->{$this->exectorAnnotation})) {
                    unset($classAnnotation);
                    continue;
                }

                foreach ($rfClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    foreach ($paramComment->parse($method->getdoccomment()) as $meta) {
                        $route = new ZRoute($class, $method->getName(), $meta['method'], $meta['path']);
                        $route->etag = isset($meta['etag']) ? $meta['etag'] : false;
                        $route->fileDefine = $method->getStartLine();
                        $classAnnotation->routes[] = $route;

                        $classAnnotation->methodUrls[$method->getName()] = $meta['path'];
                    }
                }
                $collection->add($classAnnotation->root, $classAnnotation);
            }
        }

        return $collection;
    }

    /**
     * get Annotation object
     * @return \Z\Core\Annotation\ZClassAnnotation
     */
    public function getClassAnnotation($name)
    {
        $className = $this->annotationClass;
        return new $className($name);
    }

    /**
     * set annotation class
     * @param string $className 应该是一个可以访问的类
     */
    public function setClassAnnotation($className)
    {
        $this->annotationClass = $className;
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

        $result = [];

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
        $parse->exectorActionAnnotation = $this->exectorActionAnnotation;
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