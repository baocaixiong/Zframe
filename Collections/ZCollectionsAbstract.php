<?php
/**
 * Z Collections Abstract
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Collections
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Collections;

use Z\Core\ZCore,
    Z\Exceptions\ZException;

abstract class ZCollectionsAbstract extends ZCore implements \IteratorAggregate,\ArrayAccess,\Countable
{
    /**
     * 检查一个键值是否存在 
     * 此方法继承自ArrayAccess interface
     * @param  String|Int $key 
     * @return boolean
     */
    public function offsetExists($key)
    {
        return $this->exists($key);
    }
    /**
     * 获取key的值，不存在返回null
     * 此方法继承自ArrayAccess interface
     * @param  String|Int $key 键值 
     * @return Mixed      存在返回值，否则null
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }
    /**
     * 往集合里面添加一个数据
     * 此方法继承自ArrayAccess interface
     * @param  Mixed $key   要添加的key值，如果为Null,自动往后添加数组索引
     * @param  Mixed $value 值
     * @return void
     * @throws CException 如果这个集合是只读的
     */
    public function offsetSet($key, $value)
    {
        return $this->add($key, $value);
    }
    /**
     * 从集合几面删除一个值
     * 此方法继承自ArrayAccess interface
     * @param  Mixed $key 要删除的值
     * @return Mixed      存在这个值就返回要删除的key的value,否则返回null
     * @throws CException 如果这个集合是只读的
     */
    public function offsetUnset($key)
    {
        return $this->remvoe($key);
    }

    /**
     * 获得集合的可写行
     * @return Boolean 
     */
    public function getReadOnly()
    {
        return $this->_readOnly;
    }

    /**
     * 设置集合的可写行
     * @param Boolean $value 
     * @return void
     */
    public function setReadOnly($value)
    {
        $this->_readOnly = $value;
    }
    
    /**
     * 返回对应的迭代器
     * @return \Iterator
     */
    abstract public function getIterator();
    
    abstract public function get($key);
    abstract public function remove($key);
    abstract public function add($key, $value);
    abstract public function exists($key);
    abstract public function clear();
    abstract public function getAll();
    abstract public function getCount();
    abstract public function copyFrom($data);
}