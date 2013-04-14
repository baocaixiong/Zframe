<?php
/**
 * ZModuel class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   v0.1
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core;

class ZModule extends ZCore
{
    /**
     * set configure
     * @param Array $config config array
     */
    public function setConfig($config)
    {
        $class = new \ReflectionClass($this);

        if (is_array($config)) {
            foreach ($config as $key => $value) {
                $this->$key = $value;
            }
        }
    }
}