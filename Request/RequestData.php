<?php
/**
 * ZRequestData class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Request
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Request;

use \Z\Z;

class RequestData extends \ArrayObject
{
    /**
     * request data
     * @var Array
     */
    private $_data;
    /**
     * 构造方法 
     * @param Array $arr 要转换的数组
     * @return \Z\Request\RequestData
     */
    public function __construct(Array $arr = [])
    {
        parent::__construct($arr);
        $this->_data = $arr;
        $this->setFlags(self::ARRAY_AS_PROPS);
    }

    /**
     * 不做修改requset data
     * @return Array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * 获取单个经过处理的参数
     * @param  String $name         参数键
     * @param  String $type         要处理的方式
     * @param  Mixed  $defaultValue 默认值
     * @return Mixed
     */
    public function get($name, $type, $defaultValue = null)
    {
        if (!$this->offsetExists($name)) {
            return $default;
        }
        $ret = $this->offsetGet($name);
        switch ($type) {
            case 'int':
            case 'integer':
                return (int) $ret;
                break;
            case 'boolean':
            case 'bool':
                switch ($ret) {
                case 'true':
                    return true;
                    break;
                case 'false':
                    return false;
                    break;
                default:
                    return (bool) $ret;
                    break;
                }
                break;
            case 'float':
            case 'numeric':
                return (float) $ret;
            break;
            case 'array':
                return $ret;
                break;
            default:
                return (string) $ret;
                break;
        }
    }
}