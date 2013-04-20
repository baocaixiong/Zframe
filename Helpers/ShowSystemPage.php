<?php
/**
 * show error and exception
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


class ShowSystemPage
{
	/**
     * 展示错误和异常页面
     * @param \Exception $e   异常对象或者错误数组
     * @return Void
     */
    public static function showErrorandException(\Exception $e)
    {
        $category = get_class($e);
        if ($e instanceof ZHttpException) {
            $category .= $e->getStatusCode();
        }
        $message = $e->getMessage();
        
        $file = $e->getFile();
        $line = $e->getLine();
        $traces = $e->getTrace();

        $html = '<html><head><title> ' . $category . '</title></head><body>';
        $html .= '<div style="width:1000px;margin:0 auto;margin-top:20px;padding:5px;height:auto;">
            <div style="padding:0px;margin:0;">';
        $html .= '<h3>[ ' . $category . ' ] ' . $message . '</h3>';
        $html .= '<p><strong>Throw in : </strong>' . $file . '(' . $line . ')</p>';
        $html .= '<hr />';
        $html .= '<p><strong> Stack trace: </strong></p><p style="padding:15px;">';

        foreach ($traces as $key => $trace) {
            $html .= ($key + 1) . '. ' . $trace['file'] 
            . '(' . $trace['line'] . ')' . "<br />";
        }

        $html .= '</p></div></div></body></html>';
        echo $html;
    }
}