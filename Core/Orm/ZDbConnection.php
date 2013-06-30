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
use Z\Exceptions\ZException;
use PDO;
use PDOException;
use Z\Core\Orm\Structures\ZStructureConvention;
use Z\Core\Orm\Structures\ZStructureInterface;

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
    public $charset = 'UTF-8';

    /**
     * pdo drive attributes [name => value]
     * @var array
     */
    public $pdoAttributes = array();

    public $debug;

    /**
     * 缓存对象
     * @var string
     */
    public $cache = 'cache';

    /**
     * PDO instance
     * @var \PDO
     */
    public $pdo;

    /**
     * 是否开启预处理语句。true开启。false不开启
     * @var string
     */
    public $emulatePrepare;

    public $freeze;

    /**
     * 数据库驱动名称
     * @var string
     */
    private $_driveName;

    /**
     * 事务句柄
     * @var \Z\Core\Orm\Transaction
     */
    private $_transaction;

    /**
     * 数据库表前缀
     * @var string
     */
    private $_tablePrefix = '';

    
    private $_schema;

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
                throw new ZException($message, $e->errorInfo, (int)$e->getCode(), $e);
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
        //启用或禁用预处理语句的模拟。 有些驱动不支持或有限度地支持本地预处理。
        if ($this->emulatePrepare !== null && constant('PDO::ATTR_EMULATE_PREPARES')) {
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $this->emulatePrepare);
        }

        //设置字符集
        if ($this->charset !== null && in_array($this->getDriverName(), array('pgsql', 'mysql', 'mysqli'))) {
            $this->pdo->exec('SET NAMES ' . $this->pdo->quote($this->charset));
        }

        $this->fire(self::EVENT_AFTER_OPEN);
    }
    
    /**
     * 关闭数据库连接
     * @return void
     */
    public function close()
    {
        if (!is_null($this->pdo)) {
            $this->pdo = null;
            // $this->_schema = null;
            // $this->_transaction = null;
        }
    }

    /**
     * 返回一个 包含可用 PDO 驱动名字的数组。如果没有可用的驱动，则返回一个空数组。
     * @return array
     */
    public static function getAvailableDrivers()
    {
        return PDO::getAvailableDrivers();
    }

    /**
     * get service info
     * @return string
     */
    public function getServiceInfo()
    {
        return $this->getAttribute(PDO::ATTR_SERVER_INFO);
    }

    /**
     * 设置PHP参数
     * @param  int   $code  [description]
     * @param  mixed $value [description]
     * @return void
     */
    public function setAttribute($code, $value)
    {
        if ($this->pdo instanceof PDO) {
            $this->pdo->setAttribute($code, $value);
        }
    }

    /**
     * get pdo attribute
     * 
     * @param  int $code PDO CONSTANTS
     * @return mixed
     */
    public function getAttribute($code)
    {
        return $this->pdo->getAttribute($code);
    }

    /**
     * set table prefix
     * @param  string $value table prefix
     * @return void
     */
    public function setTablePrefix($value)
    {
        if (!is_string($value)) {
            throw new ZInvalidConfigException("tablePrefix 配置必须是string");
        }

        $this->_tablePrefix = trim($value);
    }

    /**
     * get table prefix
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->_tablePrefix;
    }
    /**
     * 获得数据库驱动名称
     * from YII2
     * @return string 
     */
    public function getDriverName()
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

    public function getTableRawName($name)
    {
        if (strpos($name, $this->tablePrefix) === false) {
            return '`'. $this->tablePrefix . $name . '`';
        } else {
            return '`' . $name . '`';
        }
    }
}

