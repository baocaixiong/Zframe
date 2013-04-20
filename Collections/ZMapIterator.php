<?php
/**
 * Z ZMapIterator class 
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

use Z\ZCore,
    Z\Exceptions\ZException;

class ZMapIterator implements Iterator
{
    private $_data;

    private $_curKey;

    private $_keys;

    public function __construct(&$data)
    {
        $this->_data = $data;
        $this->_keys=array_keys($data);
        $this->_curKey=reset($this->_keys); //当前数组指针
    }

    /**
     * 当前key
     * @return String|Int
     */
    public function key()
    {
        return $this->_curKey;
    }

    /**
     * 重置当前数组集合
     * @return void
     */
    public function rewind()
    {
        $this->_key=reset($this->_keys);
    }

    /**
     * 当前值
     * @return Mixed
     */
    public function current()
    {
        return $this->_data[$this->_curKey];
    }

    /**
     * 将数组指针往后移动
     * @return void
     */
    public function next()
    {
        $this->_key = next($this->_keys);
    }

    /**
     * 返回是否在当前位置的元素
     * @return Boolean
     */
    public function valid()
    {
        return $this->_curKey !== false;
    }
}