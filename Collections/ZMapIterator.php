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

class ZMapIterator implements \Iterator
{
    /**
     * @var array the data to be iterated through
     */
    private $_data;
    /**
     * @var array list of keys in the map
     */
    private $_keys;
    /**
     * @var mixed current key
     */
    private $_curKey;

    /**
     * Constructor.
     * @param array $data the data to be iterated through
     */
    public function __construct(&$data)
    {
        $this->_data = &$data;
        $this->_keys = array_keys($data);
        $this->_surKey = reset($this->_keys);
    }

    /**
     * 重置当前数组集合
     * @return void
     */
    public function rewind()
    {
        $this->_curKey = reset($this->_keys);
    }

    /**
     * 当前key
     * @return string|int
     */
    public function key()
    {
        return $this->_curKey;
    }

    /**
     * 当前值
     * @return mixed
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
        $this->_curKey = next($this->_keys);
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