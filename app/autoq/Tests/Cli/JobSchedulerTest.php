<?php

namespace Autoq\Tests\Cli;

use Autoq\Cli\Lib\JobScheduler;
use Autoq\Lib\ScheduleParser\Schedule;
use Autoq\Tests\Autoq_TestCase;
use Phalcon\Config;
use Phalcon\Logger\Adapter\Stream;

class JobSchedulerTest extends Autoq_TestCase
{
    public function testTimeOnlySchedule()
    {
        /**
         * @var $log Stream
         */
        $log = $this->getMockBuilder(Stream::class)
            ->setConstructorArgs(['php://stderr'])
            ->setMethods(['log', 'error'])->getMock();


        $jobScheduler = new JobScheduler(self::$config, $log);

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

