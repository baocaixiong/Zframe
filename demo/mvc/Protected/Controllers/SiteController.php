<?php


namespace WebRoot\Controllers;

use Z\Z,
    Z\Executors\ZController;

/**
 * 你好啊
 * @root /test
 * @default-mapper defaultMapper
 */
class SiteController extends ZController
{
    /**
     * 
     * @param  [type] $name [description]
     * @param  [type] $id   [description]
     *
     * @http method|GET path|/ cache|300
     * @aha  asdf
     * @return [type]       [description] $name, $id = 123123   $id = 123123, $name
     */
    public function index($name = 123, $id = 123123)
    {
        return $this->httpResponse
            ->setBody('HELLO WORLD')
            ->setEtag()
            ->setExpires()
            ->setLastModified(time())
            ->setStatus(200);
    }
}


