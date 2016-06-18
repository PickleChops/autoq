<?php

namespace Autoq\Data\Queue;


class QueueFlow
{
    const STATUS_NEW = 1;
    const STATUS_FETCHING = 2;
    const STATUS_SENDING = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_ERROR = 1000;
    const STATUS_ABORTED = 2000;

    const STATE_BEGIN = 'begin';
    const STATE_END = 'end';
    const STATE_OCCURRED = 'occurred';

    static private $statusNames = [
        self::STATUS_NEW => 'NEW',
        self::STATUS_FETCHING => 'FETCHING',
        self::STATUS_SENDING => 'SENDING',
        self::STATUS_COMPLETED => 'COMPLETED',
        self::STATUS_ERROR => 'ERROR',
        self::STATUS_ABORTED => 'ABORTED'
    ];

    const STORE_STARTEND = 0;
    const STORE_OCCURED = 1;

    static private $startAndEnd = [
        self::STATE_BEGIN => null, self::STATE_END => null
    ];

    static private $occured = [
        self::STATE_OCCURRED => null
    ];

    static private $templateByStatus = [

        self::STATUS_NEW => self::STORE_STARTEND,
        self::STATUS_FETCHING => self::STORE_STARTEND,
        self::STATUS_SENDING => self::STORE_STARTEND,
        self::STATUS_COMPLETED => self::STORE_OCCURED,
        self::STATUS_ERROR => self::STORE_OCCURED,
        self::STATUS_ABORTED => self::STORE_OCCURED

    ];

    private $flowControl;

    public function __construct()
    {
        $this->flowControl = $this->buildNewFlowControl();
    }

    /**
     * @return array
     */
    private function buildNewFlowControl()
    {
        $flowControl = [];
        foreach (self::$templateByStatus as $status => $templateType) {

            if ($templateType == self::STORE_STARTEND) {
                $flowControl[$this->getStatusName($status)] = self::$startAndEnd;
            } else {
                $flowControl[$this->getStatusName($status)] = self::$occured;
            }

        }
        return $flowControl;
    }

    /**
     * @param $statusCode
     * @return $this
     * @throws \Exception
     */
    public function startStatus($statusCode)
    {

        if (self::$templateByStatus[$statusCode] != self::STORE_STARTEND) {
            throw new \Exception("You are unable to start status {$this->getStatusName($statusCode)}");
        }

        $this->flowControl[$this->getStatusName($statusCode)][self::STATE_BEGIN] = time();
        return $this;
    }

    /**
     * @param $statusCode
     * @return $this
     * @throws \Exception
     */
    public function endStatus($statusCode)
    {

        if (self::$templateByStatus[$statusCode] != self::STORE_STARTEND) {
            throw new \Exception("You are unable to end status {$this->getStatusName($statusCode)}");
        }

        $this->flowControl[$this->getStatusName($statusCode)][self::STATE_OCCURRED] = time();
        return $this;
    }

    public function setOccuredTime($statusCode)
    {

        if (self::$templateByStatus[$statusCode] != self::STORE_OCCURED) {
            throw new \Exception("You are unable to set occured time on status {$this->getStatusName($statusCode)}");
        }

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