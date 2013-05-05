<?php


namespace WebRoot\Controllers;

use Z\Z,
    Z\Executors\ZController;

/**
 * 你好啊
 * @root /test
 * @default-mapper fsdaasdf
 */
class SiteController extends ZController
{
    /**
     * 
     * @param  [type] $name [description]
     * @param  [type] $id   [description]
     *
     * @path /
     * @http-method GET
     * @return [type]       [description]
     */
    public function index($name, $id)
    {

        $this->application->getAnnotation();
        return $this->httpResponse
            ->setBody('HELLO WORLD')
            ->setEtag()
            ->setExpires()
            ->setLastModified(time())
            ->setStatus(200);
    }
}


