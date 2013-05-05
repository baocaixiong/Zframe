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
    ZParseCommentInterface;

class ZParseComment extends ZAppComponent implements ZParseCommentInterface
{

    /**
     * 丢掉的annotation
     * @var array
     */
    public $ignoredAnnotations = ['access', 'author', 'copyright', 'see','package', 
            'Id', 'param', 'return', 'version'];

    /**
     * parse comment
     * @param  string $comment comment string
     * @return array []
     */
    public function parse($comment)
    {
        $result = [];

        if (empty($comment)) {
            return $result;
        }

        if (preg_match_all('#\* @([^@\n\r\t]*)#', $comment, $matches, PREG_PATTERN_ORDER) > 0) {
            foreach ($matches[1] as $matche) {
                if (!preg_match('#([0-9a-zA-Z-]+)#', $matche, $subMatch)) {
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

                $result[] = [$name, $arguments];
            }
        }

        return $result;
    }
}

