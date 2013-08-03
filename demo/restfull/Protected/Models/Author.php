<?php


namespace Project\Models;

use Z\Core\Orm\ZModel;

class Author extends ZModel
{

    protected $tableClass = 'Project\Tables\AuthorTable';

    

    public function getAll()
    {
        var_dump($this->table->select()->fields(array('id' => 'newId', 'name'))
            // ->where('name=张三', 'and')
            //->where(array('name = 李四'), 'or')
        ->where(array('or', 'title1=123123', array('or', 'id=1', 'id=2')))
        ->where(array('like', 'name', 'xxx'), 'or')
        ->where(array('in', 'id', array(1,3,4)), 'or')
        ->join('book')
        ->orderBy('id', 'C')
        ->orderBy('name', 'C')
        ->groupBy('id, name')
        ->limit(1)
        ->fetchAll()
        );
    }
}