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
        
        preg_match_all('%!([a-z0-9/\.\\\#@$& =\<\>\:|]*)!%is', $docString, $matches, PREG_PATTERN_ORDER);

        $matches = $matches[1];
        foreach ($matches as $key => $matche) {
            $matches[$key] = preg_replace('%\ +%', ' ', $matche); //将多个空格转为一个空格
        }

        $annotations = array();
        foreach ($matches as $key => $matche) {
            $temp = explode('=', $matche);
            if (isset($temp[1])) {
                if (strpos($temp[1], '|')) {
                    $annotations[$temp[0]] = explode('|', $temp[1]);
                } else {
                    if (strtolower($temp[1]) === 'false') {
                        $annotations[$temp[0]] = false;
                    } else  {
                        $annotations[$temp[0]] = $temp[1];
                    }
                }
                
            } else {
                $annotations[$temp[0]] = true;
            }
        }

        return $annotations;
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

