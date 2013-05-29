<?php

namespace Project\Test\Resources;

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
    public function get($yyy, $xxx)
    {
        var_dump($xxx, $yyy);

    }

    /**
     * get method 
     *
     * !method=GET|POST|DELETE! !path=/123123!
     * !cache=300! !etag!
     * @return [type] [description]
     */
    public function get11($xxx=345, $yyy=123)
    {
var_dump($xxx, $yyy);
    }
}