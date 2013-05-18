<?php
/**
 * ZResponseAbstract class
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

use Z\Z,
    \Z\Core\ZAppComponent;

abstract class ZResponseAbstract extends ZAppComponent implements \ZResponseInterface
{
    /**
     * response default charset
     * @var string
     */
    public $charset = 'UTF-8';

    /**
     * default expires 1000m
     * @var integer
     */
    public $expires = 1000;

    /**
     * use HTTP protocol 1.1
     * @var string
     */
    public $protocol = 'HTTP/1.1';

    /**
     * default http status code 200
     * @var integer
     */
    public $statusCode = 200;

    /**
     * whether enable eTag
     * @var boolean
     */
    public $enableEtag = false;

    /**
     * will set header
     * @var array
     */
    protected $headers = array();

    /**
     * http body
     * @var string
     */
    protected $body = '';

    /**
     * http Content-Type 
     * @var string
     */
    protected $contentType = 'text/html';

    /**
     * HTTP status description
     * @var string
     */
    protected $statusDesc = '';

    /**
     * from Gandalf
     */
    // [Informational 1xx]
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;

    // [Successful 2xx]
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NONAUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;

    // [Redirection 3xx]
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_UNUSED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;

    // [Client Error 4xx]
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;

    // [Server Error 5xx]
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * 推荐的HTTP码描述
     * @var array
     */
    protected $recommendedStatusDesc = array(
        // [Informational 1xx]
        100 => 'Continue',
        101 => 'Switching Protocols',
        
        // [Successful 2xx]
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        
        // [Redirection 3xx]
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        
        // [Client Error 4xx]
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        
        // [Server Error 5xx]
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );
    
    /**
     * response initialize method 
     * submethod can Overloading this methid to initialize
     * @return void
     * @todo   initialize
     */
    public function initialize() 
    {
        
    }

    /**
     * set reponse not found 404
     * @return \Z\Response\ZResponseAbstract 
     */
    public function notFount()
    {
        return $this->setStatus(self::HTTP_NOT_FOUND);
    }

    /**
     * set reponse 416
     * @return \Z\Response\ZResponseAbstract 
     */
    public function notInRange ()
    {
        return $this->setStatus(self::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE);
    }

    /**
     * set reponse 400
     * @return \Z\Response\ZResponseAbstract 
     */
    public function badRequest ()
    {   
        $this->body = '';
        return $this->setStatus(self::HTTP_BAD_REQUEST);
    }

    /**
     * set reponse 403
     * @return \Z\Response\ZResponseAbstract 
     */
    public function forbidden ()
    {
        return $this->setStatus(self::HTTP_FORBIDDEN);
    }

    /**
     * set reponse 409
     * @return \Z\Response\ZResponseAbstract 
     */
    public function conflict ()
    {
        return $this->setStatus(self::HTTP_CONFLICT);
    }

    /**
     * set reponse 500
     * @return \Z\Response\ZResponseAbstract
     */
    public function internalError()
    {
        return $this->setStatus(self::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * set reponse 406
     * @return \Z\Response\ZResponseAbstract
     */
    public function notAcceptable()
    {
        $this->body = '';
        return $this->setStatus(self::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * set reponse 304
     * @return \Z\Response\ZResponseAbstract
     */
    public function notModified()
    {
        $this->body = '';
        return $this->setStatus(self::HTTP_NOT_MODIFIED);
    }

    /**
     * set reponse 201
     * @return \Z\Response\ZResponseAbstract
     */
    public function created()
    {
        return $this->setStatus(self::HTTP_CREATED);
    }
    /**
     * 获得所有的 Header 信息
     * 
     * @return array
     */
    abstract public function getAllHeaders();

    /**
     * 将要返回的内容填充，直接respond
     *
     * @param string $content 返回值
     * @return void
     */
    abstract public function output($content = '');

    /**
     * 设置头
     * @param string         $headerName header name
     * @param boolean|string $replace    replace value
     *
     * @return \Z\Response\ZResponseAbstract
     */
    abstract public function setHeader($headerName, $replace = false);

    /**
     * set response http code
     * @param int $code status code value
     *
     * @return \Z\Response\ZResponseAbstract
     */
    abstract public function setStatusCode($code);
}
