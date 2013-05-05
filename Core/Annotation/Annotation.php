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
    Z\Exceptions\ZAnnotationException;

class Annotation extends ZAppComponent implements AnnotationInterface
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
     * 缓存文件名称
     * @var string
     */
    public $cacheName = 'annotation.cache';
    /**
     * 初始化方法
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $annotationCollection = $this->getAnnotationCollection();
        $annotationCollection->separator = $this->separator;

        if (empty($this->scanDir)) {
            $this->scanDir = Z::app()->getBasePath();
        }

        if (empty($this->cacheDir)) {
            $this->cacheDir = Z::app()->getBasePath() . '/runtime/cache';
        }

        $this->checkScanDirAndCacheDir();//检查目录
        $this->collect();
    }

    /**
     * 收集
     * @return [type] [description]
     */
    public function collect()
    {
        $collection = $this->getAnnotationCollection();

        $files = $this->getAllScriptFiles($this->scanDir);

        $paramComment = $this->getParseComment();
        foreach ($files as $file) {

            $classes = $this->getClassFromFile($file);

            foreach ($classes as $class) {
                
                $rfClass = new \ReflectionClass($class);

                foreach ($paramComment->parse($rfClass->getDocComment()) as $meta) {
                    var_dump($meta,$class);
                }
            }
        }
    }

    /**
     * 从文件中获得class name
     * @param  string $file filename
     * @return array
     */
    protected function getClassFromFile($file)
    {
        $declaredClasses = get_declared_classes();//获得已经加载了的class
        include_once $file;
        $newClass = array_diff($declaredClasses, get_declared_classes());

        if (empty($newClass)) {
            foreach ($declaredClasses as $class) {
                $rfClass = new \ReflectionClass($class);
                if ($rfClass->getFileName() === $file) {
                    $newClass[] = $rfClass->getName();
                }
            }
        }

        return $newClass;
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

    /**
     * 获得document parse 组件
     * 
     * @return \Z\Core\CoreComponents\ZParseComment
     */
    protected function getParseComment()
    {
        return Z::app()->getParseComment();
    }

    /**
     * 扫描项目下面所有的php类文件
     * 
     * @param  string $dir 要扫描的目录
     * @return array 扫描得出的文件
     */
    protected function getAllScriptFiles($dir)
    {
        $files = [];
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
            throw new ZAnnotationException(Z::t('{scanDir} is not exist', ['{scanDir}' => $this->scanDir]));
        } else {
            if (!is_writeable($this->scanDir)) {
                throw new ZAnnotationException(Z::t('{scanDir} is not writeable', ['{scanDir}' => $this->scanDir]));
            }
        }

        if (!is_dir($this->cacheDir)) {
            throw new ZAnnotationException(Z::t('{cacheDir} is not exist', ['{cacheDir}' => $this->cacheDir]));
        } else {
            if (!is_writeable($this->cacheDir)) {
                throw new ZAnnotationException(Z::t('{cacheDir} is not writeable', ['{cacheDir}' => $this->cacheDir]));
            }
        }
    }
}