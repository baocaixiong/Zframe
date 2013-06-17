<?php

namespace Modules\Test\Resources;

use Z\Z;
use Z\Executors\ZResource;
use Project\Mappers\UserMapper;


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
        $userMapper = new UserMapper(Z::app()->getDb());

        var_dump($userMapper->getTable());

        // $pdo = new \PDO("mysql:dbname=not_orm;host=127.0.0.1", 'root', '123123');
        // include Z_PATH.'/NotORM/NotORM.php';
        // $db = new \NotORM(Z::app()->getDb()->pdo);
        // $books = $db->book(); //result
        

        // foreach ($books as $key => $book) {
        //     var_dump($book['title'], $book->author['name']);
        // }
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
        var_dump($s);
    }
}