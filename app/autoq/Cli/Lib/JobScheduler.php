<?php

namespace Autoq\Cli\Lib;

use Autoq\Lib\ScheduleParser\Schedule;
use Phalcon\Config;
use Phalcon\Logger\Adapter\Stream;

class JobScheduler
{

    private $config;
    private $log;

    private $timeHorizon;

    /**
     * JobScheduler constructor.
     * @param Config $config
     * @param Stream $log
     */
    public function __construct(Config $config, Stream $log)
    {
        $this->config = $config;
        $this->log = $log;
    }

    /**
     * isJobReadyToSchedule
     * @param Schedule $schedule
     * @return bool
     */
    public function isJobReadyToSchedule(Schedule $schedule)
    {
        $ready = false;

        $now = time();

        switch ($schedule->getFrequency()) {

            case Schedule::NO_FREQUENCY:
                $ready = $this->isReadyNoFrequency($schedule, $now);
                break;

            case Schedule::HOURLY:
                break;

            case Schedule::WEEKLY:
                break;

            case Schedule::DAILY:
                break;

        }

        return $ready;

    }

    /**
     * @param Schedule $schedule
     * @param $now
     * @return bool
     * @throws \Exception
     */
    private function isReadyNoFrequency(Schedule $schedule, $now)
    {

        //If this is an ASAP schedule then this job is ready to go
        if ($schedule->getAsap() === true) {
            return true;
        }

        if ($schedule->getDate() && $schedule->getTime()) {
            $this->log->error("No date or time provided for a 'No Frequency' schedule ");
            return false;
        }

        $date = $schedule->getDate() !== false ? $schedule->getDate() : date('d-M-y', $now);
        $time = $schedule->getTime() !== false ? $schedule->getTime() : '00:00';

        $runTime = strtotime("$date $time");

        $readyToSchedule = $now + $this->getTimeHorizon() >= $runTime;

        return $readyToSchedule;
    }


    /**
     * @return mixed
     */
    public function getTimeHorizon()
    {
        return $this->timeHorizon;
    }

    /**
     * @param mixed $timeHorizon
     */
    public function setTimeHorizon($timeHorizon)
    {
        $this->timeHorizon = $timeHorizon;
    }

}