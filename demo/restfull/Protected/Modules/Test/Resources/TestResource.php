<?php

namespace TestRestful\Test\Resources;

use Z\Z;
use Z\Executors\ZResource;


/**
 * TestResource
 *
 * @root /test
 * @haha asdfaf
 */
class TestResource extends ZResource
{
    /**
     * get method
     *
     * @http method|GET path|/ cache|300 etag|false
     * 
     * @return [type] [description]
     */
    public function get()
    {

    }

    /**
     * get method
     *
     * @http method|GET path|/set cache|300 etag|false
     * 
     * @return [type] [description]
     */
    public function set()
    {

    }
}

?>