<?php


namespace Autoq\Data\Queue;


class QueueItem
{
    
    private $status;
    private $statusReceived;

    private $statusHistory = [];

    private $id;
    private $created;
    private $updated;
    private $jobDefintion;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return QueueItem
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusReceived()
    {
        return $this->statusReceived;
    }

    /**
     * @param mixed $statusReceived
     * @return QueueItem
     */
    public function setStatusReceived($statusReceived)
    {
        $this->statusReceived = $statusReceived;
        return $this;
    }

    /**
     * @return array
     */
    public function getStatusHistory()
    {
        return $this->statusHistory;
    }

    /**
     * @param array $statusHistory
     * @return QueueItem
     */
    public function setStatusHistory($statusHistory)
    {
        $this->statusHistory = $statusHistory;
        return $this;
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
     * @return mixed
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