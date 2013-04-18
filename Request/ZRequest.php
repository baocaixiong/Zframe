<?php
/**
 * ZRequest class
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

use \Z\Z,
    \Z\Core\ZAppComponent;

/**
 * ZRequest class
 */
class ZRequest extends ZAppComponent implements \ZRequestInterfase
{
    public $enableXss = false;

    private $_post, $_get, $_request, $_cookie, $_put, $_delete;
    /**
     * 初始化 request
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->normalizeRequest();
    }

    /**
     * 标准化request
     * 
     * @return [type] [description]
     */
    protected function normalizeRequest()
    {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            if (isset($_GET)) {
                $this->_get = $_GET = $this->stripSlashes($_GET);
            } 
            if (isset($_POST)) {
                $this->_post = $_POST = $this->stripSlashes($_POST);
            }
            if (isset($_REQUEST)) {
                $this->_request = $_REQUEST = $this->stripSlashes($_REQUEST);
            }
            if (isset($_COOKIE)) {
                $_cookie = $_COOKIE = $this->stripSlashes($_COOKIE);
            }
        } else {
            $this->_get = $_GET;
            $this->_post = $_POST;
            $this->_cookie = $_COOKIE;
        }

        if ($this->enableXss) {
            Z::app()->attachEventHandler('onBeginRequest', array($this, 'validateXssfToken'));
        }
    }

    /**
     * 转实体
     * @param  Mixed $value 要转换的值
     * @return Mixed
     */
    protected function stripSlashes(&$value)
    {
        return is_array($value)
        ? array_map(array($this, 'stripSlashes'), $value) : stripslashes($value);
    }

    /**
     * 获得脚本名称
     * @return \Z\Request\RequestData
     */
    public function getScriptName()
    {
        return $_SERVER['SCRIPT_NAME'];
    }

    /**
     * 获得GET值
     * @return \Z\Request\RequestData
     */
    public function getGet()
    {
        return new RequestData($this->_get);
    }

    /**
     * 获得POST值
     * @return \Z\Request\RequestData
     */
    public function getPost()
    {
        $data = $this->parsePayload();

        foreach ($data as $key => $value) {
            $this->_post[$key] = $value;
        }
        return new RequestData($this->_post);
    }

    public function parseIOStreams()
    {
        $content = $this->getRawBody();
        $data = [];
        if ($content === false) {
            return $data;
        }
        switch ($this->getContentType()) {
            case 'application/json':
                $data = json_decode($content);
                break;
            default:
                parse_str($content, $data);
                break;
        }
        return is_array($data) ? $data : [];
    }

    /**
     * 获取delete 值
     * @return Z\Request\RequestData
     */
    public function getDelete()
    {
        if (!is_null($this->_put)) {
            return $this->_put;
        }
        $data = $this->parseIOStreams();
        return new RequestData($this->_put = $data);
    }
    /**
     * 获得PUT值
     * @return \Z\Request\RequestData
     */
    public function getPut()
    {
        if (!is_null($this->_put)) {
            return $this->_put;
        }
        $data = $this->parseIOStreams();
        return new RequestData($this->_put = $data);
    }

    /**
     * get user agent 
     * @return String
     */
    public function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * get request uri 
     * @return String
     */
    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * get scheme
     * @return String
     */
    public function getScheme()
    {
        return isset($_SERVER['SCHEME']) ? $_SERVER['SCHEME'] : 'http';
    }

    /**
     * get request method
     * @return String 
     */
    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);;
    }

    /**
     * get queryString 
     * @return String
     */
    public function getQueryString()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * 获取path info 信息 
     * @return String 
     */
    public function getPathInfo()
    {
        if (isset($_SERVER['PATH_INFO'])) {
            return $_SERVER['PATH_INFO'];
        }
        return null;
    }
    /**
     * check post request 
     * @return boolean 
     */
    public function isPost()
    {
        return ($this->getMethod() === 'POST');
    }

    /**
     * check ajax request
     * @return boolean 
     */
    public function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * check get request 
     * @return boolean 
     */
    public function isGet(){
        return $this->getMethod() === 'GET';
    }
    /**
     * check put request
     * @return boolean 
     */
    public function isPut(){
        return $this->getMethod() === 'PUT';
    }
    /**
     * check delete request 
     * @return boolean 
     */
    public function isDelete(){
        return $this->getMethod() === 'DELETE';
    }

    /**
     * check spider 
     * @return boolean 
     */
    public function isSpider() {
        $agent   = strtolower($_SERVER['HTTP_USER_AGENT']);
        $spiders = array(
            '/baiduspider/',
            '/sohu-search/',
            '/googlebot/',
            '/yahoo! slurp;/',
            '/msnbot/',
            '/mediapartners-google/',
            '/scooter/',
            '/yahoo-mmcrawler/',
            '/fast-webcrawler/',
            '/yahoo-mmcrawler/',
            '/yahoo! slurp/',
            '/fast-webcrawler/',
            '/fast enterprise crawler/',
            '/grub-client-/',
            '/msiecrawler/',
            '/npbot/',
            '/nameprotect/i',
            '/zyborg/i',
            '/worio bot heritrix/i',
            '/ask jeeves/',
            '/libwww-perl/i',
            '/gigabot/i',
            '/bot@bot.bot/i',
            '/seznambot/i'
        );
        foreach($spiders as $spider) {
            if(preg_match($spider, $agent)) {
                return $spider;
            }
        }
        return false;
    }

    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }
    public function getServerPort()
    {
        return $_SERVER['SERVER_PORT'];
    }
    
    public function getUrlReferrer()
    {
        return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    }
    /**
     * 获取客户机器的真实IP
     * 
     * @author houdunwangxj
     * @return String
     */
    public function getRealIp()
    {
        $ip = ''; //保存客户端IP地址
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $ip = getenv("HTTP_CLIENT_IP");
            } else {
                $ip = getenv("REMOTE_ADDR");
            }
        }
        $long = ip2long($ip);
        $clientIp = $long ? array($ip, $long) : array("0.0.0.0", 0);
        return $clientIp[$type];
    }
    /**
     * 获得真实的body体
     * @return String
     */
    public function getRawBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * return content type
     * @return String 
     */
    public function getContentType()
    {
        return explode(';', $_SERVER['CONTENT_TYPE'])[0];
    }
    /**
     * 防范处理跨站攻击
     * 
     * @return void
     */
    public function validateXssfToken()
    {
        echo '夸张攻击的处理等等';
    }
}