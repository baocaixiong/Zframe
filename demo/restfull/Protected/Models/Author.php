<?php


namespace Project\Models;

use Z\Core\Orm\ZModel;

class Author extends ZModel
{

    protected $tableClass = 'Project\Tables\AuthorTable';

    

    public function getAll()
    {
       var_dump($this->table->select());
    }
}