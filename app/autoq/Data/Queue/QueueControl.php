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
        $data['flow_control'] = (new QueueFlow())->startStatus(QueueFlow::STATUS_NEW)->getFlowControl();

        return $this->queueRepo->save($data);
    }

    /**
     * @return array
     */
    public function grabNextNewToFetch()
    {

        $this->dbConnection->begin();

        $next = $this->dbConnection->fetchOne("select id, job_def->'$.id' as job_id, json_unquote(job_def->'$.name') as job_name 
                from job_queue
    			where
                    flow_control->'$.NEW.begin' AND NOT flow_control->'$.NEW.end'
                order by flow_control->'$.NEW.begin' limit 1 for update");

        if ($next !== false && $next['id']) {

            //Add the key to id the staged resultset
            $dataStageKey = $this->makeDataStageKey($next['id'], $next['job_id'], $next['job_name']);

            $time = time();

            $update = $this->dbConnection->execute("update job_queue
                                            set flow_control = json_set(flow_control, '$.NEW.end',{$time},'$.FETCHING.new', {$time}), 
                                            data_stage_key = '{$dataStageKey}'
                                            where id = {$next['id']}");

            $this->dbConnection->commit();

            if ($update) {
                $this->log->info("Queue ID {$next['id']} reserved for fetching");
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
     * @param $queueId
     * @param $statusToEnd
     */
    public function endStatus($queueId, $statusToEnd)
    {
        $jsonPath = "$.{$statusToEnd}.end"; 
        $time = time();
        
        $this->dbConnection->execute("update job_queue 
                                        set flow_control = json_set(flow_control, '$.NEW.end',{$time})
                                        where id = {$queueId}");
        
        $this->log->info("Queue item ID: $queueId - Status");
    }


    /**
     * @param JobDefinition $jobDefinition
     * @return array
     */
    public function getLastCompletedOrActiveWithInWindow(JobDefinition $jobDefinition)
    {
        $jobId = $jobDefinition->getId();

        $last = $this->dbConnection->fetchOne("
                select * 
                from job_queue 
                where job_def->'$.id' = {$jobDefinition->getId()}
                    AND NOT (flow_control->'$.ERROR.occurred'
                     OR flow_control->'$.ABORTED.occurred' OR flow_control->'$.COMPLETED.occurred') 
                order by id DESC limit 1");

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