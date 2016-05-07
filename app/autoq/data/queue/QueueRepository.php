<?php

namespace Autoq\Data\Queue;

use Autoq\Data\BaseRepository;
use Phalcon\Db;
use Phalcon\Db\Exception;

class QueueRepository extends BaseRepository
{
    /**
     * @param $data
     * @return bool
     */
    public function save($data)
    {

        $defAsJson = $this->convertToJson($data['def'], 'Job definition');
        $status = $this->convertToJson($data['status'], 'Status');

        if ($defAsJson == null || $status == null) {
            return false;
        }

        if ($this->dBConnection->insertAsDict('job_queue', ['def' => $defAsJson, 'status' => $status]) === false) {
            $this->logger->error("Unable to save item to queue");
            return false;
        }

        return $this->dBConnection->lastInsertId();

    }

    /**
     * Fetch a job definition by id
     * @param $id
     * @return array
     */
    public function getByID($id)
    {
        try {
            $row = $this->dBConnection->fetchOne("SELECT * FROM job_queue where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

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

            $results = $this->simpleSelect('job_queue', null, null, $limit, [$this, 'hydrate']);

        } catch (Exception $e) {
            $this->logger->error("Unable to fetch queue items.");
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

            $results = $this->simpleSelect('job_queue', $whereString, null, null, [$this, 'hydrate']);


        } catch (Exception $e) {
            $this->logger->error("Unable to fetch queue items with condition: $whereString");
            return false;
        }

        return $results;

    }

    /**
     * Delete a job
     * @param $id
     * @return bool
     */
    public function delete($id)
    {

        try {

            $this->dBConnection->execute("DELETE FROM job_queue where id = :id", ['id' => $id]);

        } catch (Exception $e) {
            $this->logger->error("Unable to delete job_queue with id: $id");
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

        $defAsJson = $this->convertToJson($data['def'], 'Job definition');
        $status = $this->convertToJson($data['status'], 'Status');

        if ($defAsJson == null || $status == null) {
            return false;
        }

        try {

            $this->dBConnection->updateAsDict('job_queue', ['def' => $defAsJson, 'status' => $status], "id = $id");

        } catch (Exception $e) {
            $this->logger->error("Unable to update queue item with id: $id");
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

            $row = $this->dBConnection->fetchOne("SELECT id FROM job_queue where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->logger->error("Unable to fetch queue item with id: $id");
            return false;
        }

        return array_key_exists('id', $row);

    }


    /**
     * Build JSON representation of queue item
     * @param $row
     * @return bool|mixed
     */
    protected function hydrate($row)
    {
        $definition = $this->convertFromJson($row['def']);
        $status = $this->convertFromJson($row['status']);

        if ($definition == null || $status == null) {
            return false;
        }

        return [

            'id' => $row['id'],
            'definition' => $definition,
            'status' => $status,
            'created' => $row['created'],
            'updated' => $row['updated']

        ];
    }

}