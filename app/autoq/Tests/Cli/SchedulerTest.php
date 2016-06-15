<?php

namespace Autoq\Tests\Cli;

use Autoq\Cli\Lib\JobScheduler;
use Autoq\Cli\Scheduler;
use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Jobs\JobsRepository;
use Autoq\Data\Queue\QueueRepository;
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

        /**
         * @var $jobRepo JobsRepository
         */
        $jobRepo = $this->getMockBuilder(JobsRepository::class)
            ->setConstructorArgs([self::$di])
            ->getMock();

        /**
         * @var $queueRepo QueueRepository
         */
        $queueRepo = $this->getMockBuilder(QueueRepository::class)
            ->setConstructorArgs([self::$di])
            ->getMock();


        $scheduler = new Scheduler(self::$config, $log, $jobRepo, $queueRepo);

        $scheduler->setTimeHorizon(3600);

        $now = time();

        $timeStr = date('H:i', $now + 1800);

        $job = new JobDefinition(['schedule' => $timeStr]);


        $ready = $scheduler->isJobReadyToSchedule($job);

        //This job is set to start within the horizon so ready ought to be true
        $this->assertEquals(true,$ready);

        $timeStr = date('H:i', $now + 7200);

        $job = new JobDefinition(['schedule' => $timeStr]);


        $ready = $scheduler->isJobReadyToSchedule($job);

        //This job is set to start outside the horizon so ready ought to be false
        $this->assertEquals(false,$ready);

    }
    
}

