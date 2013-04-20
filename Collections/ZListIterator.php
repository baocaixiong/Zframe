<?php
/**
 * Z ZListIterator class 
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Helpers
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Collections;
/**
 *
 * 这个会允许ZList来遍历列表中的各个项目，返回一个新的迭代器.
 * 来自 YII
 */
class ZListIterator implements \Iterator
{
	/**
	 * @var array the data to be iterated through
	 */
	private $_d;
	/**
	 * @var integer index of the current item
	 */
	private $_i;
	/**
	 * @var integer count of the data items
	 */
	private $_c;

	/**
	 * Constructor.
	 * @param array $data the data to be iterated through
	 */
	public function __construct(&$data)
	{
		$this->_d=&$data;
		$this->_i=0;
		$this->_c=count($this->_d);
	}

	/**
	 * 重置数组指针
	 * This method is required by the interface Iterator.
	 */
	public function rewind()
	{
		$this->_i=0;
	}

	/**
	 * 返回当前循环的 key值
	 * This method is required by the interface Iterator.
	 * @return integer the key of the current array item
	 */
	public function key()
	{
		return $this->_i;
	}

	/**
	 * 返回当前值
	 * This method is required by the interface Iterator.
	 * @return mixed the current array item
	 */
	public function current()
	{
		return $this->_d[$this->_i];
	}

	/**
	 * 将指针往后移
	 * This method is required by the interface Iterator.
	 */
	public function next()
	{
		$this->_i++;
	}

	/**
	 * 返回 当前key是否在 范围之内
	 * This method is required by the interface Iterator.
	 * @return boolean
	 */
	public function valid()
	{
		return $this->_i < $this->_c;
	}
}
