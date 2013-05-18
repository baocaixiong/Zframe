<?php
/**
 * Singleton Module
 *
 * PHP Version 5.4
 *
 * @category  Z
 * @package   Helpers
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   v0.1
 * @link      http://www.baocaixiong.com
 */
namespace Z\Helpers;

trait ZSingleton
{
    protected static $__instance = array();
    
    private $_className;
    
    /**
     * Prevent users to clone the instance
     *
     * @return void
     */
    final public function __clone()
    {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR ); 
    }
    
    /**
     * create single instance of this class
     *
     * @param String $key key of the object 
     *
     * @return Mixed object
     */
    public static function getInstance()
    {
        $cls = get_called_class();
        if (!array_key_exists($cls, self::$__instance)) {
            self::$__instance[$cls] = new $cls;
            self::$__instance[$cls]->_className = $cls;
        }
        return self::$__instance[$cls];
    }
    
    /**
     * destroy self
     *
     * @return Void
     */
    public function destroy()
    {
        unset(self::$__instance[$this->_className]);
        unset($this);
    }

}