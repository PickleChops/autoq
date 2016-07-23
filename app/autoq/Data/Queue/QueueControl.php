<?php

namespace Autoq\Data\Queue;

use Autoq\Data\DataTraits;
use Autoq\Data\Jobs\JobDefinition;
use Phalcon\Config;
use Phalcon\Db;
use Phalcon\Logger\Adapter\Stream;


class QueueControl
{
    use DataTraits;

    protected $config;
    protected $log;
    protected $queueRepo;
    protected $dbConnection;

    /**
     * JobControl constructor.
     * @param Config $config
     * @param Stream $log
     * @param QueueRepository $queueRepo
     */
    public function __construct(Config $config, Stream $log, QueueRepository $queueRepo)
    {
        $this->config = $config;
        $this->log = $log;
        $this->queueRepo = $queueRepo;

        $this->dbConnection = $queueRepo->getDBConnection();
    }

    /**
     * Add new item to Queue
     * @param JobDefinition $jobDefinition
     * @return bool
     */
    public function addNew(JobDefinition $jobDefinition)
    {
        $data = [];

        //Convert jobDefinition
        $jobDefinitionData = $jobDefinition->toArray();

        $data['job_def'] = $jobDefinitionData;
        $data['flow_control'] = (new FlowControl())->setStatus(FlowControl::STATUS_NEW)->toArray();

        return $this->queueRepo->save($data);
    }

    /**
     * Coordinate locking to get a NEW queue item to fetch
     * @return array
     */
    public function grabNextNewToFetch()
    {

        //Start transaction
        $this->dbConnection->begin();

        //Select and lock next NEW queue item
        $next = $this->dbConnection->fetchOne("select id, job_def->'$.id' as job_id, json_unquote(job_def->'$.name') as job_name 
                from job_queue
    			where
                    flow_control->'$.status' = 'NEW'
                order by flow_control->'$.status_time' limit 1 for update");

        //If item is found
        if ($next !== false && $next['id']) {

            //Add the key to id the staged result set (used to cache result set)
            $dataStageKey = $this->makeDataStageKey($next['id'], $next['job_id'], $next['job_name']);

            $time = time();

            //Move the queue item on to FETCHING status
            $update = $this->
                        dbConnection->
                            execute("update job_queue
                                     set flow_control = json_set(flow_control, '$.status','FETCHING','$.status_time', {$time}, '$.status_history.FETCHING',{$time}), 
                                     data_stage_key = '{$dataStageKey}'
                                     where id = {$next['id']}");

            //Commit the transaction
            $this->dbConnection->commit();

            //Log what happened
            if ($update) {
                $this->log->info("Queue ID {$next['id']} reserved for fetching");
            } else {
                $this->log->error("There was a problem updating queue item in " . __CLASS__);
            }

            //Reread to get latest updates to row
            $next = $this->queueRepo->getById($next['id']);

        } else {
            //End commit on no NEW items found
            $this->dbConnection->commit();
        }

        return $next;

    }

    /**
     * @return array
     */
    public function grabNextToSend()
    {

        $this->dbConnection->begin();

        $next = $this->dbConnection->fetchOne("select id, job_def->'$.id' as job_id, json_unquote(job_def->'$.name') as job_name 
                from job_queue
    			where
                    flow_control->'$.status' = 'FETCHING_COMPLETE'
                order by flow_control->'$.status_time' limit 1 for update");


        if ($next !== false && $next['id']) {
            
            $time = time();

            $update = $this->dbConnection->execute("update job_queue
                                            set flow_control = json_set(flow_control, '$.status','SENDING','$.status_time', {$time}, '$.status_history.SENDING',{$time})
                                            where id = {$next['id']}");

            $this->dbConnection->commit();

            if ($update) {
                $this->log->info("Queue ID {$next['id']} reserved for sending");
            } else {
                $this->log->error("There was a problem updating queue item in " . __CLASS__);
            }

            //Reread to get latest updates to row
            $next = $this->queueRepo->getById($next['id']);

        } else {
            $this->dbConnection->commit();
        }

        return $next;

    }

    /**
     * @param JobDefinition $jobDefinition
     * @return array
     */
    public function getLastCompletedOrActiveWithInWindow(JobDefinition $jobDefinition)
    {
        $last = $this->dbConnection->fetchOne("
                select * 
                from job_queue 
                where job_def->'$.id' = {$jobDefinition->getId()}
                    AND NOT (flow_control->'$.status' = 'ERROR'
                     OR flow_control->'$.status' = 'ABORTED') 
                order by id DESC limit 1;");

        return $last;
    }

    /**
     * Return a uniqiue idenifier for a queue item
     * @param $queueItemId
     * @param $jobId
     * @param $jobName
     * @return string
     */
    public function makeDataStageKey($queueItemId, $jobId, $jobName)
    {

        //Swap the whitespace for underscores to be more filename friendly
        $result = substr(preg_replace("/\\s+/ui", "_", strtolower($jobName)), 0, 32);

        $key = "{$queueItemId}_{$jobId}_$result";

        return $key;
    }

    /**
     * @param QueueItem $queueItem
     * @param $status
     * @throws \Exception
     */
    public function updateStatus(QueueItem $queueItem, $status)
    {

        $time = time();

        $this->dbConnection->execute("update job_queue
                                      set flow_control = json_set(flow_control, 
                                        '$.status','$status','$.status_time', {$time}, 
                                        '$.status_history.{$status}', {$time},
                                        '$.error_message', '{$queueItem->getFlowControl()->getErrorMessage()}'
                                        ) 
                                      where id = {$queueItem->getId()}");


        if (($updated = $this->dbConnection->affectedRows()) != 1) {
            throw new \Exception("Incorrect update in " . __METHOD__ . " $updated items updated!");
        }

        $this->log->info("Queue Item: {$queueItem->getId()} - Status changed to $status");
    }

    /**
     * Fetch a queue item by id
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        return $this->queueRepo->getById($id);
    }

    /**
     * @param null $limit
     * @return array|bool
     */
    public function getAll($limit = null)
    {
        return $this->queueRepo->getAll($limit);
    }

    /**
     * @param null $whereString
     * @return array|bool
     */
    public function getWhere($whereString = null)
    {
        return $this->queueRepo->getWhere($whereString);
    }

    /**
     * @param $id
     * @param $data
     * @return bool
     */
    public function update($id, $data)
    {
        return $this->queueRepo->update($id, $data);
    }

    /**
     * Does a record exist for this jobID
     * @param $id
     * @return array
     */
    public function exists($id)
    {
        return $this->queueRepo->exists($id);
    }


}