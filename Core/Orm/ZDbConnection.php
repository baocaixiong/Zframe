<?php
/**
 * ZDbConnection class
 *
 * PHP Version 5.4
 *
 * @category  System
 * @package   Core\Orm\
 * @author    baocaixiong <baocaixiong@gmail.com>
 * @copyright 2013 baocaixiong.com
 * @license   Copyright (c) 2013 
 * @version   GIT:<git_id>
 * @link      http://www.baocaixiong.com
 */
namespace Z\Core\Orm;

use Z\Z;
use Z\Core\ZAppComponent;
use Z\Exceptions\ZInvalidConfigException;
use PDO;
use PDOException;

class ZDbConnection extends ZAppComponent
{
    /**
     * 定义一个afterOpen event
     */
    const EVENT_AFTER_OPEN = 'afterOpen';

    /**
     * 连接缓存KEY
     * @var string
     */
    const CACHE_CONNECTION_KEY = 'z.core.orm.zdbconnection';

    /**
     * The Data Source Name, or DSN, contains the information required to connect to the database.
     * In general, a DSN consists of the PDO driver name, followed by a colon, 
     * followed by the PDO driver-specific connection syntax. 
     * Further information is available from the PDO driver-specific documentation.
     * @see http://cn2.php.net/manual/en/pdo.construct.php
     * @var string
     */
    public $dsn;

    /**
     * database userName
     * @var string
     */
    public $userName;

    /**
     * database password
     * @var string
     */
    public $password;

    /**
     * database default charset
     * @var string
     */
    public $charset;

    /**
     * pdo drive attributes [name => value]
     * @var array
     */
    public $pdoAttributes = array();

    /**
     * 缓存对象
     * @var string
     */
    public $cache = 'cache';

    /**
     * 数据库表前缀
     * @var string
     */
    public $tablePrefix = '';

    /**
     * PDO instance
     * @var \PDO
     */
    public $pdo;

    /**
     * 预处理?
     * @var string
     */
    public $emulatePrepare;

    /**
     * 数据库驱动名称
     * @var string
     */
    private $_driveName;

    public function init()
    {
        parent::init();
        $this->createConnection();
    }

    /**
     * use pdo drive create connection
     * 
     * @return void
     */
    protected function createConnection()
    {
        if ($this->pdo === null) {
            if (empty($this->dsn)) {
                throw new ZInvalidConfigException('数据库连接:dsn配置将不能为空.');
            }
            try {
                $this->pdo = new PDO($this->dsn, $this->userName, $this->password);
            } catch (PDOException $e) {
                $message = Z_DEBUG ? '数据库连接失败:' . $e->getMessage() : '数据库连接失败';
                throw new Exception($message, $e->errorInfo, (int)$e->getCode(), $e);
            }

            $this->initConnection();
        }
    }

    /**
     * 初始化数据库连接
     *  from YII2
     * @return void
     */
    protected function initConnection()
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($this->emulatePrepare !== null && constant('PDO::ATTR_EMULATE_PREPARES')) {
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $this->emulatePrepare);
        }
        if ($this->charset !== null && in_array($this->getDriverName(), array('pgsql', 'mysql', 'mysqli'))) {
            $this->pdo->exec('SET NAMES ' . $this->pdo->quote($this->charset));
        }
        $this->fire(self::EVENT_AFTER_OPEN);
    }
    
    /**
     * 获得数据库驱动名称
     * from YII2
     * @return string 
     */
    protected function getDriverName()
    {
        if ($this->_driveName === null) {
            if (($pos = strpos($this->dsn, ':')) !== false) {
                $this->_driveName = strtolower(substr($this->dsn, 0, $pos));
            } else {
                $this->_driveName = strtolower($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
            }
        }

        return $this->_driveName;
    }
}

