<?php


namespace WebRoot\Controllers;

use Z\Z,
    Z\Executors\ZController;

class SiteController extends ZController
{
    public function index()
    {
        echo '<h1>Hello World!</h1>';
        exit;
    }
}


