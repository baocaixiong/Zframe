<?php


namespace Project\Tables;

use Z\Z;
use Z\Core\Orm\ZTable;

class AuthorTable extends ZTable
{
    protected $modelClass = 'Project\Models\Author';

    protected $tableName = 'author';

    public function setColumns()
    {
        $this->setColumn('name', 'string', '', array('max' => 100), 'newName');

        $this->foreignKey('book', 'id', BookTable::getInstance(), 'author_id');
        $this->virtualColumn('title1', 'book', 'title');
    }
}