<?php
/**
 * ZResponseFactory class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Response
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Response;

use Z\Z;
use Z\Exceptions\ZResponseException;
/**
 * create response
 */
class ZResponseFactory
{
    /**
     * response map
     * @var array
     */
    protected static $responses = array(
        'http' => 'Z\Response\ZHttpResponse',
    );

    /**
     * factory method 
     * create response
     * @param  string $responseCode response code e.g: http, json, download
     * @return \Z\Response\ZHttpResponse
     */
    public static function create($responseCode)
    {
        if (!array_key_exists($responseCode, self::$responses)) {
            throw new ZResponseException(
                Z::t('在resource中的action的annotation设置的 !response! 不正确.')
            );
        }

        $className = self::$responses[$responseCode];
        return new $className();
    }
}