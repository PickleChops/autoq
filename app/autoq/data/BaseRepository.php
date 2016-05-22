<?php

namespace Autoq\Data;

use Autoq\Services\DatabaseConnections;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di;
use Phalcon\Db;
use Phalcon\Logger\Adapter\Stream;

abstract class BaseRepository implements SqlRepositoryInterface
{

    protected $di;

    /**
     * @var $logger Stream
     */
    protected $logger;

    /**
     * @var $config Config
     */
    protected $config;

    /**
     * @var $conection Mysql
     */
    protected $dBConnection;


    /**
     * @var $conection DatabaseConnections
     */
    protected $dBConnectionService;

    /**
     * BaseRepository constructor. Set up basic services for a repository
     * @param Di $di
     */
    public function __construct(Di $di)
    {
        $this->di = $di;
        $this->config = $this->di->get('config');
        $this->logger = $this->di->get('log');
        $this->dBConnectionService = $this->di->get('dBConnectionService');
        $this->dBConnection = $this->dBConnectionService->getConnection($this->config['database']);

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
            $this->logger->error("Unable to convert $fieldName data to JSON");
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
            $this->logger->error("Unable to convert $fieldName from JSON");
            return false;
        }

        return $data;
    }


    /**
     * @param $table
     * @param $whereString
     * @param $orderString
     * @param $limitString
     * @param callable $hydrater
     * @return array|bool
     */
    protected function simpleSelect($table, $whereString, $orderString, $limitString, Callable $hydrater = null)
    {

        $whereString = $whereString == '' ? null : 'WHERE ' . $whereString;
        $orderString = $orderString == '' ? null : 'ORDER BY ' . $orderString;
        $limitString = $limitString == '' ? null : 'LIMIT ' . $limitString;

        $result = $this->dBConnection->fetchAll("SELECT * FROM `$table` $whereString $orderString $limitString", Db::FETCH_ASSOC);


        $rows = [];

        if ($hydrater !== null) {
            foreach ($result as $row) {
                $rows[] = call_user_func($hydrater, $row);
            }
        }

        return $rows;

    }
}