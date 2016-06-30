<?php


namespace Autoq\Data\Queue;


use Autoq\Data\Arrayable;
use Autoq\Data\Jobs\JobDefinition;

class QueueItem implements Arrayable
{

    private $id;
    private $created;
    private $updated;
    
    /**
     * @var $jobDefintion JobDefinition
     */
    private $jobDefintion;
    
    /**
     * @var $flowControl FlowControl
     */
    private $flowControl;
    
    private $dataStageKey;

    /**
     * QueueItem constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setJobDefintion(new JobDefinition($data['job_def']));
        $this->setCreated($data['created']);
        $this->setUpdated($data['updated']);
        $this->setDataStageKey($data['data_stage_key']);
        
        $this->setFlowControl(new FlowControl($data['flow_control']));
    }


    /**
     * Convert a queue item object back to a plain array
     * @return array
     * @throws \Exception
     */
    public function toArray()
    {
        $data = [];

        $data['id'] = $this->getId();
        $data['created'] = $this->getCreated();
        $data['updated'] = $this->getUpdated();
        $data['data_stage_key'] = $this->getDataStageKey();
        $data['flow_control'] = $this->getFlowControl()->toArray();
        $data['job_def'] = $this->getJobDefintion()->toArray();

        return $data;

    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return QueueItem
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return FlowControl
     */
    public function getFlowControl()
    {
        return $this->flowControl;
    }

    /**
     * @param FlowControl $flowControl
     */
    public function setFlowControl($flowControl)
    {
        $this->flowControl = $flowControl;
    }

    /**
     * @return mixed
     */
    public function getDataStageKey()
    {
        return $this->dataStageKey;
    }

    /**
     * @param mixed $dataStageKey
     */
    public function setDataStageKey($dataStageKey)
    {
        $this->dataStageKey = $dataStageKey;
    }
    
    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return QueueItem
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     * @return QueueItem
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return JobDefinition
     */
    public function getJobDefintion()
    {
        return $this->jobDefintion;
    }

    /**
     * @param mixed $jobDefintion
     * @return QueueItem
     */
    public function setJobDefintion($jobDefintion)
    {
        $this->jobDefintion = $jobDefintion;
        return $this;
    }
        
}