<?php

namespace TestRestful\Test\Resources;

use Z\Z;
use Z\Executors\ZResource;


/**
 * TestResource
 *
 * !Root   /test/sadf             sadf
 * !Layout /style/sadfasd
 * @haha asdfafasdfasd
 */
class TestResource extends ZResource
{
    /**
     * get method
     *
     * @http method|POST path|/ cache|300 etag|false
     * 
     * @return [type] [description]
     */
    public function get()
    {

    }

    /**
     * get method
     *
     * !Route method|GET path|/seta
     * !Cache cache|300  etag|false
     * !Cache aaa|aaa
     * @return [type] [description]
     */
    public function set($name, $ma)
    {

    }

    /**
     * get method
     *
     * !Route method|GET path|/seta
     * !Cache cache|300  etag|false
     * !Cache aaa|aaaasdf
     * @return [type] [description]
     */
    public function set1()
    {

    }

    /**
     * get method 
     *
     * !method=GET! !path=/! !cache=300! !etag!
     * @return [type] [description]
     */
    protected function get111()
    {

    }
}