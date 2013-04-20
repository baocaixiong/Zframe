<?php
/**
 * Configure Register
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Helpers
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Helpers;

use Z\Core\ZConfigureRegisterInterface;

class ConfigureRegister implements ZConfigureRegisterInterface
{
    use \Z\Helpers\ZSingleton;

    private static $_config = [];

    /**
     * get application configure
     * @param String $optionName 想要获得配置的名字 可以是 以 / 隔开的参数
     * @param Mixed  $default    如果配置不存在，会返回的默认值
     * 
     * @return Mixed 根据配置返回数据
     */
    public function getConfig($optionName = null, $default = null)
    {
        if ($optionName === null) {
            return self::$_config;
        } elseif (strpos($optionName, '/') === false) {
            return array_key_exists($optionName, self::$_config)
                ? self::$_config[$optionName] : $default;
        } else {
            $configPars = explode('/', $optionName);
            $config = &self::$_config;
            foreach ($configPars as $configPar) {
                if (!isset($pos[$configPar])) {
                    return $default;
                }
                $config = &$config[$configPar];
            }
            return $config;
        }
    }

    /**
     * set application config 
     * 
     * @param String | Array $option      要设置的配置项
     * 可以使用 / 分开，这样会作为数组的子
     * @param String | Array $optionValue 配置项的值
     * @return Void
     */
    public function setConfig($option, $optionValue = null)
    {
        if (is_array($option)) {
            foreach ($option as $key => $value) {
                $this->setConfig($key, $value);
            }
            return $this;
        }

        if (strpos($option, '/') === false) {
            self::$_config[$option] = $optionValue;
            return $this;
        } else {
            $configPars = explode('/', $option);
            $config = &self::$_config;

            $depth = count($configPars) - 1;

            for ($i = 0; $i <= $depth; $i++) {
                $configPar = $configPars[$i];
                if ($i < $depth) {
                    if (!isset($config[$configPar])) {
                        $config[$configPar] = array();
                    }
                    $config = &$config[$configPar];
                } else {
                    $config[$configPar] = $optionValue;
                }
            }
        }
        return $this;
    }
}