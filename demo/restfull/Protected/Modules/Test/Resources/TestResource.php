<?php

namespace TestRestful\Test\Resources;

use Z\Z;
use Z\Executors\ZResource;


/**
 * TestResource
 *
 * !root=/test! !layout=index.html    sadf!
 * !asdfasd=as\df!
 * !etag=false!
 */
class TestResource extends ZResource
{
    /**
     * get method 
     *
     * !method=GET! !path=/haha/$xxxx! !cache=300! !etag!
     * @return [type] [description]
     */
    public function get111()
    {

    }
}