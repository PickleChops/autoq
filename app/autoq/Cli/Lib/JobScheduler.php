<?php

namespace Autoq\Cli\Lib;

use Autoq\Lib\ScheduleParser\Schedule;

class JobScheduler
{
    private $timeHorizon;

    /**
     * JobScheduler constructor.
     */
    public function __construct()
    {
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
     */
    private function isReadyNoFrequency(Schedule $schedule, $now) {
        
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