<?php

namespace Modules\Test\Resources;

use Z\Z;
use Z\Executors\ZResource;
use Project\Mappers\BookMapper;
use Project\Tables\BookTable;


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
        $bookTable = BookTable::getInstance();

        $bookTable->where('author_id')->limit(2, 1);

        //$bookTable->insert(array('title' => '你好啊', 'author_id' => 1));
        var_dump($bookTable->__toString());
        //var_dump($bookTable->fetch()->setProperty('title', '123123')->save());exit;
        foreach ($bookTable as $key => $value) {
            var_dump($value->title . '==' . $value->author->name . '.id = ' . $value->id);
        }
        // $userMapper = new BookMapper(Z::app()->getDb());

        // $userMapper->getAll();

        // $pdo = new \PDO("mysql:dbname=not_orm;host=127.0.0.1", 'root', '123123');
        // include Z_PATH.'/NotORM/NotORM.php';
        // $db = new \NotORM(Z::app()->getDb()->pdo);
        // $books = $db->book(); //result
           
        // var_dump($books);
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