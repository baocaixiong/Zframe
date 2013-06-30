<?php


namespace Project\Models;

use Z\Core\Orm\ZModel;

class Author extends ZModel
{

    protected $tableClass = 'Project\Tables\AuthorTable';

    

    public function getAll()
    {
        $ret = [];
        foreach ($this->zTable as $key => $value) {
            $ret[] = $value;
        }
        return $ret;
    }
}