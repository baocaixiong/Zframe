<?php


namespace WebRoot\Controllers;

use Z\Z,
    Z\Executors\ZController;

class SiteController extends ZController
{
    public function index($name, $id)
    {   var_dump($this->application->getAnnotation());
        return $this->httpResponse
            ->setBody('HELLO WORLD')
            ->setEtag()
            ->setExpires()
            ->setLastModified(time())
            ->setStatus(200);
    }
}


