<?php
/**
 * ZCache Abstract class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Caching
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */

namespace Z\Caching;

use Z\Z;

class ZFileCache extends ZCacheAbstract
{
    /**
     * 文件缓存后缀
     * @var string
     */
    public $cacheFileSuffix = '.cache';

    public $cachePath;

    /**
     * 删除缓存的几率
     * @var int
     */
    public $gcProbability = 10;

    /**
     * 初始化FileCache
     * 设置缓存路径
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->cachePath = Z::app()->getBasePath() . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'cache';

        if (!is_dir($this->cachePath)) {
            @mkdir($this->cachePath, 0777, true);
        }
    }

    /**
     * 如果缓存已经过期，向缓存中添加数据
     * @param string  $key    cache key
     * @param mixed   $value  cache value
     * @param integer $expire expire time
     */
    public function addValue($key, $value, $expire = 0)
    {
        $cacheFile = $this->getCacheFile($key);
        if (@filemtime($cacheFile) > time()) {
            return false;
        }
        return $this->setValue($key, $value, $expire);
    }

    /**
     * 添加缓存
     * @param string  $key    cache key 
     * @param mixed   $value  cache value
     * @param integer $expire expire time
     */
    public function setValue($key, $value, $expire = 0)
    {
        if ($expire < 0) {
            $expire = 31536000; // 1 year
        }

        $expire += time();

        $cacheFileName = $this->getCacheFile($key);
        if (@file_put_contents($cacheFileName, $value, LOCK_EX)) {
            @chmod($cacheFileName, 0777);
            return @touch($cacheFileName, $expire);
        }
    }

    /**
     * get Value from cache file
     * @param  string $key     cache key
     * @param  mixed  $default if cache is not exist, return default
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        $cacheFileName = $this->getCacheFile($key);

        if (@filemtime($cacheFileName) > time()) {
            return @file_get_contents($cacheFileName);
        } elseif (!is_null($default)) {
            return $default;
        }

        return false;
    }

    /**
     * 移除缓存(删除缓存文件)
     * @param  string $key cache key
     * @return boolean
     */
    public function removevalue($key)
    {
        $cacheFile = $this->getCacheFile($key);
        return @unlink($cacheFile);
    }

    /**
     * 清空缓存，会清空所有的缓存
     * @return boolean
     */
    public function flushValues()
    {
        $this->gc(true, false);
        return true;
    }

    /**
     * 清空缓存
     * @param  boolean $force       [description]
     * @param  boolean $expiredOnly [description]
     * @return void
     */
    public function gc($force = false, $expiredOnly = true)
    {
        if ($force || mt_rand(0, 1000000) < $this->gcProbability) {
            $this->gcRecursive($this->cachePath, $expiredOnly);
        }
    }

    /**
     * Recursively removing expired cache files under a directory.
     * This method is mainly used by [[gc()]].
     * @param string $path the directory under which expired cache files are removed.
     * @param boolean $expiredOnly whether to only remove expired cache files. If false, all files
     * under `$path` will be removed.
     */
    protected function gcRecursive($path, $expiredOnly)
    {
        if (($handle = opendir($path)) !== false) {
            while (($file = readdir($handle)) !== false) {
                if ($file[0] === '.') {
                    continue;
                }
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($fullPath)) {
                    $this->gcRecursive($fullPath, $expiredOnly);
                    if (!$expiredOnly) {
                        @rmdir($fullPath);
                    }
                } elseif (!$expiredOnly || $expiredOnly && @filemtime($fullPath) < time()) {
                    @unlink($fullPath);
                }
            }
            closedir($handle);
        }
    }

    /**
     * 获得缓存文件名称
     * @param  string $key cache key
     * @return string cache file name
     */
    public function getCacheFile($key)
    {
        return $this->cachePath . DIRECTORY_SEPARATOR . $key . $this->cacheFileSuffix;
    }
}