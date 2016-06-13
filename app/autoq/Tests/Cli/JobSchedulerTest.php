<?php

namespace Autoq\Tests\Cli;

use Autoq\Cli\Lib\JobScheduler;
use Autoq\Lib\ScheduleParser\Schedule;
use Autoq\Tests\Autoq_TestCase;

class JobSchedulerTest extends Autoq_TestCase
{
    public function testTimeOnlySchedule()
    {
        $jobScheduler = new JobScheduler();

        $jobScheduler->setTimeHorizon(3600);

        $schedule = new Schedule();

        $now = time();

        $timeStr = date('H:i', $now + 1800);

        $schedule->setTime($timeStr);

        $ready = $jobScheduler->isJobReadyToSchedule($schedule);

        //This job is set to start within the horizon so ready ought to be true
        $this->assertEquals(true,$ready);

        $timeStr = date('H:i', $now + 7200);

        $schedule->setTime($timeStr);

        $ready = $jobScheduler->isJobReadyToSchedule($schedule);

        //This job is set to start outside the horizon so ready ought to be false
        $this->assertEquals(false,$ready);

    }
    
}

