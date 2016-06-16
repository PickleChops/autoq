<?php

namespace Autoq\Data\Jobs;

use Autoq\Data\BaseRepository;
use Phalcon\Db;
use Phalcon\Db\Exception;

class JobsRepository extends BaseRepository
{
    /**
     * @param $data
     * @return bool
     */
    public function save($data)
    {

        if (($defAsJson = json_encode($data)) === false) {
            $this->logger->error("Unable to convert job defintion to json");
            return false;
        }

        if ($this->dBConnection->insertAsDict('job_defs', ['def' => $defAsJson]) === false) {
            $this->logger->error("Unable to save job definiton");
            return false;
        }

        return $this->dBConnection->lastInsertId();

    }

    /**
     * Fetch a job definition by id
     * @param $id
     * @return JobDefinition
     */
    public function getById($id)
    {

        try {

            $row = $this->dBConnection->fetchOne("SELECT * FROM job_defs where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->logger->error("Unable to fetch job with id: $id");
            return false;
        }

        return $row === false ? [] : $this->hydrate($row);

    }

    /**
     * @param null $limit
     * @return array|bool
     */
    public function getAll($limit = null)
    {
        try {

            $results = $this->simpleSelect($this->dBConnection, 'job_defs', null, null, $limit, [$this, 'hydrate']);

        } catch (Exception $e) {
            $this->logger->error("Unable to fetch jobs.");
            return false;
        }

        return $results;
    }

    /**
     * @param null $whereString
     * @return array|bool
     */
    public function getWhere($whereString = null)
    {

        try {

            $jobs = $this->simpleSelect($this->dBConnection, 'job_defs', $whereString, null, null, [$this, 'hydrate']);

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

        try {

            $this->dBConnection->execute("DELETE FROM job_defs where id = :id", ['id' => $id]);

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


        if (($defAsJson = json_encode($data)) === false) {
            $this->logger->error("Unable to convert job defintion to json");
            return false;
        }

        try {

            $this->dBConnection->updateAsDict('job_defs', ['def' => $defAsJson], "id = $id");

        } catch (Exception $e) {
            $this->logger->error("Unable to update job with id: $id");
            return false;
        }

        return true;
    }

    /**
     * Does a record exist for this jobID
     * @param $id
     * @return array
     */
    public function exists($id)
    {

        try {

            $row = $this->dBConnection->fetchOne("SELECT id FROM job_defs where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->logger->error("Unable to read job with id: $id");
            return false;
        }

        return $row ? array_key_exists('id', $row) : false;

    }


    /**
     * Decode JSON representation of job defintion
     * @param $row
     * @return bool|mixed
     */
    protected function hydrate($row)
    {

        $definitionData = json_decode($row['def'], true);


        if (json_last_error() != JSON_ERROR_NONE) {
            $this->logger->error("Unable to interpret job definition");
            return false;
        } else {

            $definitionData['id'] = $row['id'];
            $definitionData['created'] = $row['created'];
            $definitionData['updated'] = $row['updated'];


            $jobDefinition = new JobDefinition($definitionData);

        }

        return $jobDefinition;

    }


}