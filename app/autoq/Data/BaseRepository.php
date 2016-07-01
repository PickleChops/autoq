<?php

namespace Autoq\Data;

use Autoq\Services\DbConnectionMgr;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di;
use Phalcon\Db;
use Phalcon\Logger\Adapter\Stream;

abstract class BaseRepository implements SqlRepositoryInterface
{

    use DataTraits;

    /**
     * @var $di Di
     */
    protected $di;

    /**
     * @var $log Stream
     */
    protected $log;

    /**
     * @var $config Config
     */
    protected $config;

    /**
     * @var $conection Mysql
     */
    protected $dBConnection;
    
    /**
     * @var $conection DbConnectionMgr
     */
    protected $dBConnectionMgr;

    /**
     * BaseRepository constructor. Set up basic services for a repository
     * @param Di $di
     */
    public function __construct(Di $di)
    {
        $this->di = $di;
        $this->config = $this->di->get('config');
        $this->log = $this->di->get('log');
        $this->dBConnectionMgr = $this->di->get('dBConnectionMgr');
        $this->dBConnection = $this->dBConnectionMgr->getConnection('mysql');

    }

    /**
     * @param $data
     * @return mixed
     */
    abstract public function save($data);

    /**
     * @param $id
     * @return mixed
     */
    abstract public function exists($id);


    /**
     * @param $id
     * @return mixed
     */
    abstract public function getById($id);

    /**
     * @param null $limit
     * @return mixed
     */
    abstract public function getAll($limit = null);

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    abstract public function update($id, $data);

    /**
     * @param $id
     * @return mixed
     */
    abstract public function delete($id);

    /**
     * @param null $whereString
     * @return mixed
     */
    abstract public function getWhere($whereString = null);

    /**
     * @param $data
     * @param null $fieldName
     * @return bool
     */
    protected function convertToJson($data, $fieldName = null)
    {
        if (($json = json_encode($data)) === false) {
            $this->log->error("Unable to convert $fieldName data to JSON");
            return false;
        }

        return $json;
    }

    /**
     * @param $data
     * @param null $fieldName
     * @return bool|mixed
     */
    protected function convertFromJson($data, $fieldName = null)
    {

        $data = json_decode($data, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            $this->log->error("Unable to convert $fieldName from JSON");
            return false;
        }

        return $data;
    }

    /**
     * @return Mysql
     */
    public function getDBConnection()
    {
        return $this->dBConnection;
    }
    
    

}