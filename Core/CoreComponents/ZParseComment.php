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
    public $exectorActionAnnotation = 'http';

    public $routeKey = array(
        'method', 'path', 'cache', 'etag'
    );
    /**
     * 丢掉的annotation
     * @var array
     */
    public $ignoredAnnotations = array('access', 'author', 'copyright', 'see', 'package', 
            'id', 'param', 'return', 'version', 'throws');

    /**
     * parse comment
     * @param  string $comment comment string
     * @return array []
     */
    public function parse($comment)
    {
        $result = array();

        if (empty($comment)) {
            return $result;
        }

        if (preg_match_all('#\* @([^@\n\r\t]*)#', $comment, $matches, PREG_PATTERN_ORDER) > 0) {
            foreach ($matches[1] as $matche) {
                if (!preg_match('#([0-9a-zA-Z]+)#', $matche, $subMatch)) {
                    continue;
                }

                $name = $subMatch[1];
                if (empty($name) || in_array($name, $this->ignoredAnnotations)) {
                    continue;
                }
                $matche = preg_replace('/\ +/', ' ', $matche); //将多个空格转为一个空格
                $arguments = explode(' ', $matche);

                if (count($arguments) > 0) {
                    array_shift($arguments);
                } else {
                    $arguments = [];
                }

                $result[] = array($name, $arguments);
            }
        }

        return $this->_parseHttpRoute($result);
    }

    /**
     * parse http route
     * 
     * @param  array $annotations annotations
     * @return array
     */
    protected function _parseHttpRoute($annotations)
    {
        $httpResult = [];
        foreach ($annotations as $key => $annotation) {
            if ($annotation[0] === $this->exectorActionAnnotation) {
                foreach ($annotation[1] as $value) {
                    $temp = explode('|', $value);
                    if (!in_array($temp[0], $this->routeKey)) {
                        var_dump($temp[0]);
                        throw new ZAnnotationException(
                            Z::t("route key error, {key}", array('{key}' => $temp[0]))
                        );
                    }
                    if (isset($temp[1])) {
                        if (strtolower($temp[1]) === 'false') {
                            $httpResult[$temp[0]] = false;
                        } else {
                            $httpResult[$temp[0]] = $temp[1];
                        }
                    } else {
                        $httpResult[$temp[0]] = true;
                    }
                }

                $annotations[$key] = $httpResult;
            }
        }
        return $annotations;
    }
}

