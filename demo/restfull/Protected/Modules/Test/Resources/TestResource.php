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
    // *
    //  * get method 
    //  *
    //  * !method=POST! !path=/haha/$xxxx! !cache=300! !etag!
    //  * @return [type] [description]
     
    // public function get111()
    // {

    // }

    /**
     * get method 
     *
     * !method=GET|POST|DELETE! !path=/haha/<$xxx:string>/<$yyy:string>!
     * !cache=300! !etag!
     * @return [type] [description]
     */
    public function get($xxx, $yyy)
    {

    }
}