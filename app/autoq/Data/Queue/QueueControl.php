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
        unset($jobDefinitionData['created']);
        unset($jobDefinitionData['updated']);

        $data['job_def'] = $jobDefinitionData;
        $data['flow_control'] = (new QueueFlow())->startStatus(QueueFlow::STATUS_NEW)->getFlowControl();

        return $this->queueRepo->save($data);
    }

    public function getNextNewToProcess() {

        

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
                where job_def->'$.id' = {$jobId} 
                    AND NOT (flow_control->'$.ERROR' OR flow_control->'$.ABORTED' OR flow_control->'$.COMPLETED') 
                order by id DESC limit 1");
        
        return $last;
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