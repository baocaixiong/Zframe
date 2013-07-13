<?php


namespace Project\Tables;

use Z\Z;
use Z\Core\Orm\ZTable;

class BookTable extends ZTable
{
    protected $modelClass = 'Project\Models\Book';

    protected $tableName = 'book';

    public function setColumns()
    {
        $this->setColumn('title', 'string', '', array('max' => 100));
        $this->setColumn('author_id', 'int');

        $this->foreignKey('author', 'author_id', AuthorTable::getInstance(), 'id');
//        $this->virtualField('authorName', 'author', 'name');
    }
}
