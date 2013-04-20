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
class ZWebRequest extends ZAppComponent implements \ZRequestInterfase
{
    public $enableXss = false;

    private $_post, $_get, $_request, $_cookie, $_put, $_delete;

    private $_requestUri, $_pathInfo, $_scriptName, $_baseUrl;
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
                $_GET = $this->stripSlashes($_GET);
            } 
            if (isset($_POST)) {
                $_POST = $this->stripSlashes($_POST);
            }
            if (isset($_REQUEST)) {
                $_POST = $this->stripSlashes($_REQUEST);
            }
            if (isset($_COOKIE)) {
                $_COOKIE = $this->stripSlashes($_COOKIE);
            }
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
        if (is_null($this->_scriptName)) {
            return $this->_scriptName = $_SERVER['SCRIPT_NAME'];
        }
        return $this->_scriptName;
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
     * 获取COOKIE
     * @return \Z\Request\RequestData
     */
    public function getCookies()
    {
        return new RequestData($_COOKIE);
    }

    /**
     * 获得POST值
     * @return \Z\Request\RequestData
     */
    public function getPost()
    {
        if (!is_null($this->_post)) {
            return $this->_post;
        }
        $data = $this->parseIOStreams();
        $post = [];
        foreach ($data as $key => $value) {
            $post[$key] = $value;
        }
        return $this->_post = new RequestData($post);
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
        if (!is_null($this->_delete)) {
            return $this->_delete;
        }
        $data = $this->parseIOStreams();

        $delete = [];
        foreach ($data as $key => $value) {
            $delete[$key] = $value;
        }
        return $this->_post = new RequestData($delete);
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

        $put = [];
        foreach ($data as $key => $value) {
            $put[$key] = $value;
        }
        return $this->_post = new RequestData($put);
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
        if (is_null($this->_requestUri)) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $this->_requestUri=$_SERVER['REQUEST_URI'];
                if (!empty($_SERVER['HTTP_HOST'])) {
                    if ((strpos($this->_requestUri, $_SERVER['HTTP_HOST'])) !== false) {
                        $this->_requestUri = preg_replace(
                            '/^\w+:\/\/[^\/]+/', '', $this->_requestUri
                        );
                    } else {
                        $this->_requestUri = preg_replace(
                            '/^(http|https):\/\/[^\/]+/i', '', $this->_requestUri
                        );
                    }
                }
            } else {
                throw new ZException("Your Server is IIS or Other?");
            }
        }

        return $this->_requestUri;
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
        if (is_null($this->_pathInfo)) {

            if (isset($_SERVER['PATH_INFO'])) {
                $pathInfo = $this->decodePathInfo(trim($_SERVER['PATH_INFO'], '/'));
                if (strpos($pathInfo, '%')) {
                    $pathInfo = $this->decodePathInfo($pathInfo);
                }
                return $this->_pathInfo = $pathInfo;
            }

            $pathInfo = $this->getRequestUri();

            if (($pos = strpos($pathInfo, '?')) !== false) {
                $pathInfo = substr($pathInfo, 0, $pos);
            }

            $pathInfo = $this->decodePathInfo($pathInfo);

            $scriptName = $this->getScriptName();

            if (strpos($pathInfo, $scriptName)) {
                $pathInfo = substr($pathInfo, strlen($scriptName));
            } elseif (strpos($_SERVER['PHP_SELF'], $scriptName) === 0) {
                $pathInfo = substr($pathInfo, strlen($_SERVER['PHP_SELF']));
            } else {
                throw new ZException(
                    Z::t('ZFrame is unable to determine the path info of the request.')
                );
            }

            $this->_pathInfo = trim($pathInfo, '/');;
        }
        
        return $this->_pathInfo;
    }

    /**
     * decode path info  from Yii
     * @param  String $pathInfo pathInfo 
     * @return String
     */
    protected function decodePathInfo($pathInfo)
    {
        $pathInfo = urldecode($pathInfo);

        // is it UTF-8?
        // http://w3.org/International/questions/qa-forms-utf-8.html
        if (preg_match(
            '%^(?:
           [\x09\x0A\x0D\x20-\x7E]            # ASCII
         | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
         | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
         | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
         | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
         | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
         | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
         | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )*$%xs', $pathInfo
        )) {
            return $pathInfo;
        } else {
            return utf8_encode($pathInfo);
        }
    }

    /**
     * 判断是否为POST请求 
     * @return boolean 
     */
    public function isPost()
    {
        return ($this->getMethod() === 'POST');
    }

    /**
     * 判断是否为AJAX请求
     * @return boolean 
     */
    public function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * 判断是否为GET请求
     * @return boolean 
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }
    /**
     * 判断是否为PUT请求
     * @return boolean 
     */
    public function isPut()
    {
        return $this->getMethod() === 'PUT';
    }
    /**
     * 判断是否为DELETE请求
     * @return boolean 
     */
    public function isDelete()
    {
        return $this->getMethod() === 'DELETE';
    }

    /**
     * 判断是否为搜索引擎 
     * @return boolean 
     */
    public function isSpider()
    {
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
        foreach ($spiders as $spider) {
            if (preg_match($spider, $agent)) {
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
        static $rawBody;
        if ($rawBody === null) {
            $rawBody = file_get_contents('php://input');
        }
        return $rawBody;
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