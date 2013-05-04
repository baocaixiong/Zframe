<?php
/**
 * ZHttpResponse class
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

class ZHttpResponse extends ZResponseAbstract
{
    /**
     * 获得所有的 Header 信息
     * @return array
     */
    public function getAllHeaders ()
    {
        $protocol = $this->protocol. " {$this->getStatusCode()} {$this->getStatusDesc()}";
        $contentType = "Content-Type: {$this->contentType}; charset={$this->charset}";
        $poweredBy = 'X-Powered-By: Zframe';
        
        $arr[$protocol]     = true;
        $arr[$contentType]  = true;
        $arr[$poweredBy]    = true;
        foreach ($this->headers as $key => $value) {
            $str = "{$key}: {$value}";
            $arr[$str] = isset($value[1]) && $value[1];
        }
        
        return $arr;
    }

    /**
     * 将要返回的内容填充，直接respond
     * @param  string $content body content
     * @return void
     */
    public function output ($content = '')
    {
        $this->body = $content;
        $this->respond();
    }

    /**
     * set expire 
     * default 1000m
     * @param integer $expires expire time 
     */
    public function setExpires ($expires = 1000)
    {
        $this->setHeader('Cache-Control', 'max-age=' . $expires, true)
            ->setHeader('Expires', date('r', time() + $expires))
            ->setLastModified(time());

        return $this;
    }

    /**
     * set last modify time
     * @param  int $timestamp timestamp
     * @return \Z\Response\ZHttpResponse
     */
    public function setLastModified ($timestamp)
    {
        $this->setHeader('Last-Modified', date('r', $timestamp));
        return $this;
    }

    /**
     * force browser no cache
     * @return \Z\Response\ZHttpResponse
     */
    public function forceNoCache()
    {
        $this->setHeader("Expires", "Mon, 26 Jul 1997 05:00:00 GMT")
            ->setHeader("Cache-Control", "no-cache, must-revalidate")
            ->setHeader("Pragma", "no-cache");

        return $this;
    }

    /**
     * 增加头
     * alias method with setHeader
     * @param  string         $headerName header name
     * @param  boolean|string $replace    replace value
     * @return \Z\Response\ZHttpResponse
     */
    public function addHeader ($headerName, $replace = false)
    {
        return $this->setheader($headerName, $replace);
    }

    /**
     * 增加头
     * @param  string         $headerName header name
     * @param  boolean|string $replace    replace value
     * @return \Z\Response\ZHttpResponse
     */
    public function setHeader ($headerName, $replace = false)
    {
        $headername = str_replace(["\n", "\r"], ['', ''], $headerName);
        $this->headers[$headerName] = $replace !== false ? $replace : false;

        return $this;
    }

    /**
     * set reponse body content 
     * @param  string $content body content
     * @return \Z\Response\ZHttpResponse
     */
    public function setBody ($content = '')
    {
        $this->body = $content;
        return $this;
    }

    /**
     * get reposne body
     * @return string
     */
    public function getBody ()
    {
        return $this->body;
    }

    /**
     * 单独设置etag 是否开启
     * 默认值是 开启
     * @param  boolean $status etag status
     * @return \Z\Response\ZHttpResponse
     */
    public function setEtagAble ($status = true)
    {
        $this->enableEtag = $status;
        return $this;
    }

    /**
     * set response etag 
     * @return \Z\Response\ZHttpResponse
     */
    public function setEtag ()
    {
        if ($this->enableEtag) {
            $etag = md5($this->body);
            $this->setHeader('ETag', $etag);
        }
        return $this;
    }

    /**
     * set response 
     * if you are not set the description, Zframe will use recommendedStatusDesc
     * @param int    $code        response code
     * @param string $description response description
     * @return \Z\Response\ZHttpResponse
     */
    public function setStatus ($code, $description = '')
    {
        $this->setStatusCode($code);
        if (empty($description)) {
            $description = $this->recommendedStatusDesc[$code];
        }
        $this->setStatusDesc($description);

        return $this;
    }

    /**
     * set response status description 
     * @param  string $statusDesc status description
     * @return \Z\Response\ZHttpResponse
     */
    public function setStatusDesc ($statusDesc)
    {
        $this->statusDesc = $statusDesc;
        return $this;
    }

    /**
     * get reponse status description
     * @return string
     */
    public function getStatusDesc ()
    {
        return $this->statusDesc;
    }
    /**
     * set response http code
     * @param  int $code status code value
     * @return \Z\Response\ZHttpResponse
     */
    public function setStatusCode ($code)
    {
        $this->statusCode = intval($code);
        return $this;
    }

    /**
     * get reponse status code
     * @return int
     */
    public function getStatusCode ()
    {
        return $this->statusCode;
    }
    /**
     * get a header value 
     * @param  string $key header name
     * @return string|null
     */
    public function getHeader ($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key][0] : null;
    }

    /**
     * set reponse content type
     * @param  string $contentType content-type
     * @return \Z\Response\ZHttpResponse
     */
    public function setContentType ($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }
}
