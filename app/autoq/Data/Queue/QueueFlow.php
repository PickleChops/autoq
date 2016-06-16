<?php

namespace Autoq\Data\Queue;


class QueueFlow
{
    const STATUS_NEW = 1;
    const STATUS_FETCHING = 2;
    const STATUS_FETCH_COMPLETE = 3;
    const STATUS_SENDING = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_ERROR = 1000;
    const STATUS_ABORTED = 2000;

    const STATE_BEGIN = 'begin';
    const STATE_END = 'end';

    static private $statusNames = [
        self::STATUS_NEW => 'NEW',
        self::STATUS_FETCHING => 'FETCHING',
        self::STATUS_FETCH_COMPLETE => 'FETCH_COMPLETE',
        self::STATUS_SENDING => 'SENDING',
        self::STATUS_COMPLETED => 'COMPLETED',
        self::STATUS_ERROR => 'ERROR',
        self::STATUS_ABORTED => 'ABORTED'
    ];

    private $flowControl;

    public function __construct()
    {

        $flowControl = [];
        foreach (self::$statusNames as $status) {
            $flowControl[$status] = [self::STATE_BEGIN => null, self::STATE_END => null];
        }

        $this->flowControl = $flowControl;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function startStatus($statusCode)
    {
        $this->flowControl[$this->getStatusName($statusCode)][self::STATE_BEGIN] = time();
        return $this;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function endStatus($statusCode)
    {
        $this->flowControl[$this->getStatusName($statusCode)][self::STATE_END] = time();
        return $this;
    }


    /**
     * GetStatusName
     * @param $statusCode
     * @return mixed
     */
    private function getStatusName($statusCode)
    {
        return self::$statusNames[$statusCode];
    }

    /**
     * @return array
     */
    public function getFlowControl()
    {
        return $this->flowControl;
    }




}