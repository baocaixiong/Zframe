<?php
/**
 * parse comment 
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT: <git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\CoreComponents;

use Z\Z,
    Z\Core\ZAppComponent,
    ZParseCommentInterface,
    Z\Exceptions\ZAnnotationException;

class ZParseComment extends ZAppComponent implements ZParseCommentInterface
{
    const SEPARATOR = '|';

    /**
     * parse comment
     * @param  string $docString class or method comment
     * @return array
     */
    public function parse($docString)
    {
        $result = array();

        if (empty($docString)) {
            return $result;
        }

        preg_match_all('#!(\S+)[^\n\r\S]*(.*)#', $docString, $matches, PREG_PATTERN_ORDER);

        $annotations = $matches[1]; //类似 * !Root /test   => $annotations = ['Root']

        $values = $matches[2]; //类似* !Root /test  => $values = ['/test'];
        //annotations 和 values 的值是一一对应的
        
        if (empty($annotations)) {
            return $result;
        }

        foreach ($annotations as $key => $annotation) {
            $values[$key] = preg_replace('/\ +/', ' ', $values[$key]); //将多个空格转为一个空格
            
            if (isset($result[$annotation])) {
                $result[$annotation] = $values[$key] . ' ' . $result[$annotation];
            } else {
                $result[$annotation] = $values[$key];
            }
        }

        return $result;
    }

    /**
     * 分析comment的每一行
     * @param  string $meta comment meta
     * @return array
     */
    public function parseMeta($meta)
    {
        $metas = explode(' ', $meta);

        $result = array();
        foreach ($metas as $value) {
            if (strpos($value, self::SEPARATOR)) {
                $values = explode(self::SEPARATOR, $value);
                $result[$values[0]] = $values[1];
            } else {
                $result[$value] = false;
            }
        }
        
        return $result;
    }
}

