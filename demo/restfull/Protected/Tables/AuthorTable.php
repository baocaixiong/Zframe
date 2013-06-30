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
        $this->setColumn('name', 'string', '', array('max' => 100));

    }
}