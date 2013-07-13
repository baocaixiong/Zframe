<?php


namespace Project\Models;

use Z\Core\Orm\ZModel;

class Author extends ZModel
{

    protected $tableClass = 'Project\Tables\AuthorTable';

    

    public function getAll()
    {
        var_dump($this->table->select()->fields(array('id' => 'newId', 'name'))
        ->where(array('and', 'title1=1', array('or', 'id=1', 'id=2')))
        ->where(array('like', 'name', 'xxx'), array(), 'or')
        ->where(array('in', 'id', array(1,3,4)), array(), 'and')
        ->__toString()
        );
    }
}