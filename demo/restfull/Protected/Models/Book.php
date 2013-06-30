<?php


namespace Project\Models;

use Z\Core\Orm\ZModel;

class Book extends ZModel
{

    public $title;

    public $author_id;

    public $id;

    protected $tableClass = 'Project\Tables\BookTable';
}