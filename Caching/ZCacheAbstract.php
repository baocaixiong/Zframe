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
use Z\Core\ZObject;
use ZCachingInterface;
use Z\Exceptions\ZInvalidVariableException;
use Z\Exceptions\ZInvalidConfigException;

abstract class ZCacheAbstract extends ZObject implements ZCachingInterface, \ArrayAccess
{
    /**
     * 缓存key前缀
     * @var string
     */
    public $keyPrefix;

    /**
     * 初始化方法
     * 此方法在ZObject的__construct方法中调用
     * @throws \Z\Exceptions\ZInvalidConfigException
     * @return void
     */
    public function init()
    {
        if (is_null($this->keyPrefix)) {
            $this->keyPrefix = substr(md5(Z::app()->getId()), 0, 10);
        } elseif (!ctype_alnum($this->keyPrefix)) {
            throw new ZInvalidConfigException(Z::t('无效的配置{class}::keyPrefix', array('{class}' => get_class($this))));
        }
    }

    /**
     * get cache value
     * @param string $key     cache key
     * @param int    $expire  cache time
     * @param mixed  $default if $key is not exist return $default
     * @return mixed cache value
     */
    public function get($key, $expire = 0, $default = null)
    {
        $key = $this->buildKey($key);
        $value = $this->getValue($key, $expire, $default);

        if (empty($value) && is_null($default)) {
            return $value;
        } elseif (empty($value) && !is_null($default)) {
            return $default;
        } else {
            $value = unserialize($value);
        }

        return $value[0];
    }

    /**
     * add a cache value
     * @param  string $key    cache key
     * @param  mixed  $value  cache value
     * @param  int    $expire expires time seconds
     */
    public function add($key, $value, $expire = 0)
    {
        $key = $this->buildKey($key);

        $value = serialize(array($value));

        return $this->addValue($key, $value, $expire);
    }

    /**
     * add a cache value
     * @param  string $key    cache key
     * @param  mixed  $value  cache value
     * @param  int    $expire expires time seconds
     */
    public function set($key, $value, $expire = 0)
    {
        $key = $this->buildKey($key);

        $value = serialize(array($value));

        return $this->setValue($key, $value, $expire);
    }
    /**
     * remove a cache 
     * @return void
     */
    public function remove($key)
    {
        $key = $this->buildKey($key);
        return $this->removeValue($key);
    }
    
    /**
     * 清空所有缓存
     * @return void
     */
    public function flush()
    {
        return $this->flushValues();
    }

    /**
     * build a cache key
     * @param  string $key cache key
     * @throws \Z\Exceptions\ZInvalidVariableException
     * @return string
     */
    public function buildKey($key)
    {
        if (!is_string($key)) {
            throw new ZInvalidVariableException(Z::t("缓存key是无效的"));
        } else {
            $key = md5($key);
        }

        return $this->keyPrefix . $key;
    }

    /**************ArrayAccess implements***********************/
    /**
     * get a cache value
     * required by the interface ArrayAccess.
     * @param  string $key cache key
     * @return mixed 
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * 检测key是否存在
     * required by the interface ArrayAccess.
     * @param string $key cache key
     * @return boolean
     */
    public function offsetExists($key)
    {
        return $this->get($key) !== false;
    }

    /**
     * 给一个cache key 添加值
     * @param  string $key   cache key
     * @param  mixed  $value want to set cache value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * 删除一个缓存
     * required by the interface ArrayAccess.
     * @param  string $key cache key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->remvoe($key);
    }

    /*****************子类必须实现的Method*******************************/
    /**
     * 设置缓存
     * @param  string  $key    此key已经由set或者add方法加密
     * @param  mixed   $value  cache value
     * @param  integer $expire 过期时间
     * @return void 
     */
    abstract function setValue($key, $value, $expire = 0);
    
    abstract function addValue($key, $value, $expire = 0);

    abstract function getValue($key, $expire, $default = null);

    abstract function flushValues();

    abstract function removeValue($key);
}