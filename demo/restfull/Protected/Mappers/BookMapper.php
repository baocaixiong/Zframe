<?php


namespace Project\Mappers;


use Z\Core\Orm\ZMapper;
class BookMapper extends ZMapper
{
    protected $tableName = 'book';

    public $modelClass = 'Project\Models\Book';
}