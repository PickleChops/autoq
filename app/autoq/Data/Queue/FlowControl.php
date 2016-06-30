<?php

namespace Autoq\Data\Queue;


use Autoq\Data\Arrayable;

class FlowControl implements Arrayable
{
    const STATUS_NEW = 'NEW';
    const STATUS_FETCHING = 'FETCHING';
    const STATUS_FETCHING_COMPLETE = 'FETCHING_COMPLETE';
    const STATUS_SENDING = 'SENDING';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_ERROR = 'ERROR';
    const STATUS_ABORTED = 'ABORTED';


    private $status;
    private $statusTime;
    private $statusHistory = [

        self::STATUS_NEW => null,
        self::STATUS_FETCHING => null,
        self::STATUS_FETCHING_COMPLETE => null,
        self::STATUS_SENDING => null,
        self::STATUS_COMPLETED => null,
        self::STATUS_ERROR => null,
        self::STATUS_ABORTED => null

    ];

    private $errorMessage;

    /**
     * FlowControl constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct($data = [])
    {
        if(array_key_exists('status',$data) && array_key_exists('statusTime', $data) && array_key_exists('statusHistory', $data)) {
            
            $this->setStatus($data['status']);
            $this->setStatusTime($data['statusTime']);
            $this->setStatusHistory($data['statusHistory']);
            $this->setErrorMessage(null);
            
        }
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param mixed $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    
    
    /**
     * @return mixed
     */
    public function toArray()
    {
        return [
            'status' => $this->status,
            'status_time' => $this->statusTime,
            'status_history' => $this->statusHistory,
            'error_message' => $this->errorMessage
        ];
    }

    /**
     * @param $status
     * @param $time
     * @return $this
     */
    public function setStatus($status, $time = null) {

        if($time === null) {
            $time = time();
        }

        $this->status = $status;
        $this->statusHistory[$status] = $time;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusTime()
    {
        return $this->statusTime;
    }

    /**
     * @param mixed $statusTime
     */
    public function setStatusTime($statusTime)
    {
        $this->statusTime = $statusTime;
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
     */
    public function setStatusHistory($statusHistory)
    {
        $this->statusHistory = $statusHistory;
    }
    
    
    
}