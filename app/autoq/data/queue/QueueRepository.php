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

        $defAsJson = $this->convertToJson($data['job_def'], 'Job definition');
        $flowControl = $this->convertToJson($data['flow_control'], 'Flow control');

        if ($defAsJson == null || $flowControl == null) {
            return false;
        }

        if ($this->dBConnection->insertAsDict('job_queue', ['job_def' => $defAsJson, 'flow_control' => $flowControl]) === false) {
            $this->log->error("Unable to save item to queue");
            return false;
        }

        return $this->dBConnection->lastInsertId();

    }

    /**
     * Fetch a job definition by id
     * @param $id
     * @return QueueItem
     */
    public function getById($id)
    {
        try {

            $row = $this->dBConnection->fetchOne("SELECT * FROM job_queue where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch queue item with id: $id");
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

            $results = $this->simpleSelect($this->dBConnection,'job_queue', null, null, $limit, [$this, 'hydrate']);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch queue items.");
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

            $jobs = $this->simpleSelect($this->dBConnection, 'job_queue', $whereString, null, null, [$this, 'hydrate']);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch queue items with condition: $whereString");
            return false;
        }

        return $jobs;

    }

    /**
     * Delete a queue item
     * @param $id
     * @return bool
     */
    public function delete($id)
    {

        try {

            $this->dBConnection->execute("DELETE FROM job_queue where id = :id", ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to delete queue with id: $id");
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
            $this->log->error("Unable to convert job defintion to json");
            return false;
        }

        try {

            $this->dBConnection->updateAsDict('job_defs', ['def' => $defAsJson], "id = $id");

        } catch (Exception $e) {
            $this->log->error("Unable to update job with id: $id");
            return false;
        }

        return true;
    }

    /**
     * Does a record exist for this queue item ID
     * @param $id
     * @return array
     */
    public function exists($id)
    {

        try {

            $row = $this->dBConnection->fetchOne("SELECT id FROM job_queue where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to read queue with id: $id");
            return false;
        }

        return $row ? array_key_exists('id', $row) : false;

    }


    /**
     * Hydrate a queue item record
     * @param $row
     * @return bool|mixed
     */
    protected function hydrate($row)
    {
        $queueItemData = [];

        $jobDefinitionData = $this->convertFromJson($row['job_def'], 'job_def');
        $flowControl = $this->convertFromJson($row['flow_control'], 'flow_control');

        $queueItemData['job_def'] = $jobDefinitionData;
        $queueItemData['flow_control'] = $flowControl;

        if ($jobDefinitionData === false || $flowControl === false) {
            $this->log->error("Unable to interpret queue item");
            return false;
        } else {

            $queueItemData['id'] = $row['id'];
            $queueItemData['created'] = $row['created'];
            $queueItemData['updated'] = $row['updated'];
            $queueItemData['data_stage_key'] = $row['data_stage_key'];


            $queueItem = new QueueItem($queueItemData);

        }

        return $queueItem;

    }


}