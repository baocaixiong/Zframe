<?php
/**
 * Z Map class CMap实现了一个键名==键值对的集合. 
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

class ZMap extends ZCore implements \IteratorAggregate, \ArrayAccess, \Countable
{

    private $_readOnly = false;

    private $_data = array();

    public function __construct($data = null, $readOnly = false)
    {
        if (!is_null($data)) {
            $this->copyFrom($data);
        }
        $this->_readOnly = $readOnly;
    }

    /**
     * 复制内容
     * 会将$this->_data 清空
     * @param  Array $data 
     * @return void
     */
    public function copyFrom($data)
    {
        if (is_array($data) || $data instanceof Traversable) {
            if ($this->getCount() > 0) {
                $this->truncate();
            }
            if ($data instanceof ZMap) {
                $data=$data->_data;
            }
            foreach ($data as $key => $value) {
                $this->add($key, $value);
            }
        }
    }

    /**
     * 返回一个ZMap的迭代器 
     * @return \Z\Collections\ZMapIterator
     */
    public function getIterator()
    {
        return new ZMapIterator($this->_data);
    }
    /**
     * 返回集合中的所有的键值 
     * 
     * @return Array
     */
    public function getKeys()
    {
        return array_keys($this->_data);
    }

    /**
     * 集合数据的个数
     * 此方法继承CountAble interface 
     * @return Int
     */
    public function count()
    {
        return $this->getCount();
    }
    /**
     * 获得集合内容的个数
     * * 此方法继承自Countable接口
     * @return int 个数
     */
    public function getCount()
    {
        return count($this->_data);
    }

    /**
     * 往集合里面添加一个数据
     * @param  Mixed $key   要添加的key值，如果为Null,自动往后添加数组索引
     * @param  Mixed $value 值
     * @return void
     * @throws CException 如果这个集合是只读的
     */
    public function add($key, $value)
    {
        if (!$this->_readOnly) {
            if (null === $key) {
                $this->_data[] = $value;
            } else {
                $this->_data[$key] = $value;
            }
        } else {
           throw new ZException(Z::t('The map is read only.'));
        }
    }

    /**
     * 从集合几面删除一个值
     * @param  Mixed $key 要删除的值
     * @return Mixed      存在这个值就返回要删除的key的value,否则返回null
     * @throws CException 如果这个集合是只读的
     */
    public function remove($key)
    {
        if (!$this->_readOnly) {
            if (isset($this->_data[$keu])) {
                $value = $this->_data[$key];
                unset($this->_data[$key]);
                return $value;
            } else {
                usset($this->_data[$key]);
                return null;
            }
        } else {
            throw new ZException(Z::t('The map is read only.'));
        }
    }

    /**
     * 清空整个集合
     * @return void
     */
    public function clear()
    {
        foreach (array_keys($this->_d) as $key) {
            $this->remove($key);
        }
    }

    /**
     * 获取key的值，不存在返回null
     * @param  String|Int $key 键值 
     * @return Mixed      存在返回值，否则null
     */
    public function get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }
        return null;
    }

    /**
     * 获得所有数据
     * @return Array
     */
    public function getAll()
    {
        return $this->_data;
    }
    
    /**
     * 检查一个键值是否存在 
     * @param  String|Int $key 
     * @return boolean
     */
    public function exists($key)
    {
        return isset($this->_data[$key]) || array_key_exists($key, $this->_data);
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
     * 合并数组
     * @param  Array $a [description]
     * @param  Array $b [description]
     * @return Array
     */
    public static function mergeArray($a,$b)
    {
        $args=func_get_args();
        $res=array_shift($args);
        while (!empty($args)) {
            $next=array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    isset($res[$k]) ? $res[]=$v : $res[$k]=$v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k]=self::mergeArray($res[$k], $v);
                } else {
                    $res[$k]=$v;
                }
            }
        }
        return $res;
    }
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
}


