<?php


namespace Project\Tables;

use Z\Z;
use Z\Core\Orm\ZTable;

class BookTable extends ZTable
{

    protected $tableName = 'book';

    public function setColumns()
    {
        $this->setColumn('title', 'string', '', array('max' => 100));
    }
}
