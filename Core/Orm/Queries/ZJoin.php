<?php
/**
 * ZJoin class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\Queries
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm\Queries;

use Z\Z;
use Z\Core\Orm\Schema\ZTableSchema;
use Z\Core\Orm\Exceptions\ZJoinFieledException;

class ZJoin extends ZQuery
{
    /**
     * current table schema
     * @var \Z\Core\Orm\Schema\ZTableSchema
     */
    protected $tableSchema;

    protected $joinString = '';

    protected $joined = array();

    protected $joinArray = array();

    /**
     * CONSTRUCT METHOD
     *
     * @return \Z\Core\Orm\Queries\Mysql\ZJoin
     */
    public function __construct(ZTableSchema $tableSchema)
    {
        $this->tableSchema = $tableSchema;
    }


    public function createJoinString($foreignName, $relation = 'LEFT')
    {
        if (isset($this->joined[$foreignName])) {
            if ($this->joined[$foreignName] === $relation) {
                return;
            } else {
                unset($this->joined[$foreignName]);
                unset($this->joinArray[$foreignName]);
            }
        }

        $foreignInstance = $this->tableSchema->foreignKeys[$foreignName];

        $this->joinArray[$foreignName] =  $relation . $this->_createJoinString($foreignInstance) . ' ';

        $this->joined[$foreignName] = $relation;
    }

    private function _createJoinString($foreignInstance)
    {
        $string = ' JOIN ' . $foreignInstance->getTableRawName() . ' ON ';

        $string .= $this->tableSchema->rawName . '.' . $foreignInstance->leftField . '=';

        $string .= $foreignInstance->getTable()->getTableSchema()->rawName . '.' . $foreignInstance->rightField;

        return $string;
    }

    public function __toString()
    {
        if (empty($this->joinString)) {
            $this->joinString = implode(' ', $this->joinArray);
        }

        return "\n" . $this->joinString;
    }
}