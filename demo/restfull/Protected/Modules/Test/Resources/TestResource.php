<?php

namespace Project\Test\Resources;

use Z\Z;
use Z\Executors\ZResource;


/**
 * TestResource
 *
 * !root=true! !layout=index.html    sadf!
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
     * !method=GET|POST|DELETE! !path=/haha/<$i:string>/<$j:string>!
     * !cacheTime=300! !etag! !response=http!
     * @return [type] [description]
     */
    public function get($i, $j = 123)
    {
        echo '当前resource: TestResource<br/>';
        echo 'route参数: '. $i . '<====>' . $j;
    }

    /**
     * get method 
     *
     * !method=GET|POST! !path=/123123!
     * !cacheTime=300! !etag!
     * !response=http!
     * @return [type] [description]
     */
    public function get11($xxx=345, $yyy=123)
    {
var_dump($xxx, $yyy);
        $this->response->setHeader('xxx', 'xxx');
    }

    /**
     * get method 
     *
     * !method=GET! !path=/<$s:string>!
     * !cacheTime=300! !etag!
     * @return [type] [description]
     */
    public function get1111($s)
    {
        echo $s;
    }
}