<?php
/**
 * Z List class  实现一个数组索引的集合
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

class ZList extends ZCore implements \IteratorAggregate,\ArrayAccess,\Countable
{
    //IteratorAggregate 创建外部迭代器的接口
    //ArrayAccess       提供像访问数组一样访问对象的能力的接口。
    private $_readOnly = false;

    private $_count = 0;

    private $_data = [];

    public function __construct($data = null, $readOnly = false)
    {
        if (!is_null($data)) {
            $this->copyFrom($data);
        }
        $this->_readOnly = $readOnly;
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
     * 返回一个ZMap的迭代器 
     * @return \Z\Collections\ZListIterator
     */
    public function getIterator()
    {
        return new ZListIterator($this->_data);
    }

    /**
     * 返回集合内数据个数
     * 继承自Countable interface.
     * @return Int 
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * 返回集合内数据个数
     * 继承自Countable interface.
     * @return Int the 
     */
    public function getCount()
    {
        return $this->_count;
    }

    /**
     * Returns the item at the specified offset.
     * This method is exactly the same as {@link offsetGet}.
     * @param Int $index the index of the item
     * @return mixed the item at the index
     * @throws CException if the index is out of the range
     */
    public function itemAt($index)
    {
        if(isset($this->_data[$index]))
            return $this->_data[$index];
        elseif($index>=0 && $index<$this->_count) // in case the value is null
            return $this->_data[$index];
        else
            throw new ZException(Yii::t('yii','List index "{index}" is out of bound.',
                array('{index}'=>$index)));
    }

    /**
     * Appends an item at the end of the list.
     * @param mixed $item new item
     * @return Int the zero-based index at which the item is added
     */
    public function add($item)
    {
        $this->insertAt($this->_count,$item);
        return $this->_count-1;
    }

    /**
     * Inserts an item at the specified position.
     * Original item at the position and the next items
     * will be moved one step towards the end.
     * @param Int $index the specified position.
     * @param mixed $item new item
     * @throws CException If the index specified exceeds the bound or the list is read-only
     */
    public function insertAt($index,$item)
    {
        if(!$this->_readOnly)
        {
            if($index===$this->_count)
                $this->_data[$this->_count++]=$item;
            elseif($index>=0 && $index<$this->_count)
            {
                array_splice($this->_data,$index,0,array($item));
                $this->_count++;
            }
            else
                throw new CException(Yii::t('yii','List index "{index}" is out of bound.',
                    array('{index}'=>$index)));
        }
        else
            throw new ZException(Yii::t('yii','The list is read only.'));
    }

    /**
     * Removes an item from the list.
     * The list will first search for the item.
     * The first item found will be removed from the list.
     * @param mixed $item the item to be removed.
     * @return Int the index at which the item is being removed
     * @throws CException If the item does not exist
     */
    public function remove($item)
    {
        if(($index=$this->indexOf($item))>=0)
        {
            $this->removeAt($index);
            return $index;
        }
        else
            return false;
    }

    /**
     * Removes an item at the specified position.
     * @param Int $index the index of the item to be removed.
     * @return mixed the removed item.
     * @throws CException If the index specified exceeds the bound or the list is read-only
     */
    public function removeAt($index)
    {
        if(!$this->_readOnly)
        {
            if($index>=0 && $index<$this->_count)
            {
                $this->_count--;
                if($index===$this->_count)
                    return array_pop($this->_data);
                else
                {
                    $item=$this->_data[$index];
                    array_splice($this->_data,$index,1);
                    return $item;
                }
            }
            else
                throw new CException(Yii::t('yii','List index "{index}" is out of bound.',
                    array('{index}'=>$index)));
        }
        else
            throw new CException(Yii::t('yii','The list is read only.'));
    }

    /**
     * Removes all items in the list.
     */
    public function clear()
    {
        for($i=$this->_count-1;$i>=0;--$i)
            $this->removeAt($i);
    }

    /**
     * @param mixed $item the item
     * @return boolean whether the list contains the item
     */
    public function contains($item)
    {
        return $this->indexOf($item)>=0;
    }

    /**
     * @param mixed $item the item
     * @return Int the index of the item in the list (0 based), -1 if not found.
     */
    public function indexOf($item)
    {
        if(($index=array_search($item,$this->_data,true))!==false)
            return $index;
        else
            return -1;
    }

    /**
     * @return array the list of items in array
     */
    public function get()
    {
        return $this->_data;
    }

    /**
     * Copies iterable data into the list.
     * Note, existing data in the list will be cleared first.
     * @param mixed $data the data to be copied from, must be an array or object implementing Traversable
     * @throws CException If data is neither an array nor a Traversable.
     */
    public function copyFrom($data)
    {
        if(is_array($data) || ($data instanceof Traversable))
        {
            if($this->_count>0)
                $this->clear();
            if($data instanceof CList)
                $data=$data->_d;
            foreach($data as $item)
                $this->add($item);
        }
        elseif($data!==null)
            throw new CException(Yii::t('yii','List data must be an array or an object implementing Traversable.'));
    }

    /**
     * Merges iterable data into the map.
     * New data will be appended to the end of the existing data.
     * @param mixed $data the data to be merged with, must be an array or object implementing Traversable
     * @throws CException If data is neither an array nor an iterator.
     */
    public function mergeWith($data)
    {
        if(is_array($data) || ($data instanceof Traversable))
        {
            if($data instanceof CList)
                $data=$data->_d;
            foreach($data as $item)
                $this->add($item);
        }
        elseif($data!==null)
            throw new CException(Yii::t('yii','List data must be an array or an object implementing Traversable.'));
    }

    /**
     * Returns whether there is an item at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param Int $offset the offset to check on
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return ($offset>=0 && $offset<$this->_count);
    }

    /**
     * Returns the item at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param Int $offset the offset to retrieve item.
     * @return mixed the item at the offset
     * @throws CException if the offset is invalid
     */
    public function offsetGet($offset)
    {
        return $this->itemAt($offset);
    }

    /**
     * Sets the item at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param Int $offset the offset to set item
     * @param mixed $item the item value
     */
    public function offsetSet($offset,$item)
    {
        if($offset===null || $offset===$this->_count)
            $this->insertAt($this->_count,$item);
        else
        {
            $this->removeAt($offset);
            $this->insertAt($offset,$item);
        }
    }

    /**
     * Unsets the item at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param Int $offset the offset to unset item
     */
    public function offsetUnset($offset)
    {
        $this->removeAt($offset);
    }
}