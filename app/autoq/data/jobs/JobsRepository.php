<?php

namespace Api\data\jobs;


use Api\data\SqlRepositoryInterface;
use Phalcon\Config;
use Phalcon\Db;
use Phalcon\Db\Exception;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di;
use Phalcon\Logger\Adapter\Stream;

class JobsRepository implements SqlRepositoryInterface
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
    protected $connection;

    /**
     * @param Di $di
     */
    public function __construct(Di $di)
    {
        $this->di = $di;
        $this->config = $this->di->get('config');
        $this->logger = $this->di->get('log');

        $this->connection = $this->getConnection();
    }

    /**
     * Try and get connection to DB
     */
    public function getConnection()
    {
        $connection = null;

        try {

            $connection = new Mysql(array(
                'host' => $this->config['database']['host'],
                'username' => $this->config['database']['username'],
                'password' => $this->config['database']['password'],
                'dbname' => $this->config['database']['dbname'],
                'port' => $this->config['database']['port'],
            ));

        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $connection;
    }


    /**
     * @param $data
     * @return bool
     */
    public function save($data)
    {

        if (!($this->connection instanceof Mysql)) {
            $this->logger->error("No database connection");
            return false;
        }

        if (($defAsJson = json_encode($data)) === false) {
            $this->logger->error("Unable to convert job defintion to json");
            return false;
        }

        if ($this->connection->insertAsDict('job_defs', ['def' => $defAsJson]) === false) {
            $this->logger->error("Unable to save job definiton");
            return false;
        }

        return $this->connection->lastInsertId();

    }

    /**
     * Fetch a job definition by id
     * @param $id
     * @return array
     */
    public function getByID($id)
    {

        if (!($this->connection instanceof Mysql)) {
            $this->logger->error("No database connection");
            return false;
        }

        try {

            $row = $this->connection->fetchOne("SELECT * FROM job_defs where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->logger->error("Unable to fetch job with id: $id");
            return false;
        }

        return $row === false ? [] : $this->hydrateJson($row);

    }

    /**
     * @return array|bool
     */
    public function getAll()
    {
        return $this->getWhere(null);
    }

    /**
     * @param null $whereString
     * @return array|bool
     */
    public function getWhere($whereString = null)
    {

        if (!($this->connection instanceof Mysql)) {
            $this->logger->error("No database connection");
            return false;
        }

        try {

            $result = $this->connection->fetchAll($this->addWhere("SELECT * FROM job_defs", $whereString), Db::FETCH_ASSOC);

            $jobs = [];
            foreach ($result as $row) {
                $jobs[] = $this->hydrateJson($row);
            }

        } catch (Exception $e) {
            $this->logger->error("Unable to fetch jobs with condition: $whereString");
            return false;
        }

        return $jobs;

    }

    /**
     * Delete a job
     * @param $id
     * @return bool
     */
    public function delete($id)
    {

        if (!($this->connection instanceof Mysql)) {
            $this->logger->error("No database connection");
            return false;
        }

        try {

             $this->connection->execute("DELETE FROM job_defs where id = :id", ['id' => $id]);

        } catch (Exception $e) {
            $this->logger->error("Unable to delete job with id: $id");
            return false;
        }

        return true;
    }

    /**
     * update a job
     * @param $id
     * @param $data
     * @return bool
     */
    public function update($id, $data)
    {

        if (!($this->connection instanceof Mysql)) {
            $this->logger->error("No database connection");
            return false;
        }

        if (($defAsJson = json_encode($data)) === false) {
            $this->logger->error("Unable to convert job defintion to json");
            return false;
        }
        
        try {

            $success = $this->connection->updateAsDict('job_defs', ['def' => $defAsJson], "id = $id");

        } catch (Exception $e) {
            $this->logger->error("Unable to update job with id: $id");
            return false;
        }

        return $success;
    }

    /**
     * Does a record exist for this jobID
     * @param $id
     * @return array
     */
    public function exists($id)
    {

        if (!($this->connection instanceof Mysql)) {
            $this->logger->error("No database connection");
            return false;
        }

        try {

            $row = $this->connection->fetchOne("SELECT id FROM job_defs where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->logger->error("Unable to fetch job with id: $id");
            return false;
        }

        return array_key_exists('id', $row);

    }


    /**
     * Build JSON representation of job defintion
     * @param $row
     * @return bool|mixed
     */
    private function hydrateJson($row)
    {

        $definition = json_decode($row['def'], true);

        if (json_last_error() != JSON_ERROR_NONE) {
            $this->logger->error("Unable to interpret job definition");
            return false;
        } else {

            $definition['id'] = $row['id'];
            $definition['created'] = $row['created'];
            $definition['updated'] = $row['updated'];

        }

        return $definition;

    }

    /**
     * @param $query
     * @param $condition
     * @return string
     */
    private function addWhere($query, $condition)
    {
        return $condition ? "$query WHERE $condition" : $query;
    }


}