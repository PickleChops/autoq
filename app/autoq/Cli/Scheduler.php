<?php

namespace Autoq\Cli;

use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Jobs\JobsRepository;
use Autoq\Data\Queue\QueueControl;
use Autoq\Lib\ScheduleParser\Schedule;
use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class Scheduler implements CliTask
{

    protected $config;
    protected $log;
    protected $jobsRepo;
    protected $queueControl;

    protected $args;

    protected $timeHorizon;

    /**
     * Scheduler constructor.
     * @param Config $config
     * @param Stream $log
     * @param JobsRepository $jobRepo
     * @param QueueControl $queueControl
     */
    public function __construct(Config $config, Stream $log, JobsRepository $jobRepo, QueueControl $queueControl)
    {
        $this->config = $config;
        $this->log = $log;
        $this->jobsRepo = $jobRepo;
        $this->queueControl = $queueControl;
    }

    /**
     * Off we go
     * @param array $args
     */
    public function main(Array $args = [])
    {
        $this->log->info("Scheduler started");

        //Poll database looking for new jobs to schedule
        while (true) {

            //Get the currently defined jobs
            $this->log->debug("Looking for job definitions...");

            /**
             * @var $jobs JobDefinition[]
             */
            $jobs = $this->jobsRepo->getAll();

            $this->log->debug(count($jobs) . " job definitions found");

            // Loop through the jobs looking for jobs that are ready to schedule

            foreach ($jobs as $job) {

                if (($last = $this->queueControl->getLastCompletedOrActiveWithInWindow($job)) === false) {

                    if ($this->isJobReadyToSchedule($job)) {

                        $queueID = $this->queueControl->addNew($job);

                        $this->logForJob($job, "Added to queue with queue ID: $queueID");

                    }
                } else {
                    $this->logForJob($job, "This job is already scheduled in the queue (queue ID: {$last['id']})",Logger::DEBUG);
                }
            }

            sleep($this->config->app->scheduler_sleep);
        }

    }

    /**
     * isJobReadyToSchedule
     * @param JobDefinition $job
     * @return bool
     */
    public function isJobReadyToSchedule(JobDefinition $job)
    {
        $ready = false;
        $now = time();
        $schedule = $job->getSchedule();

        switch ($schedule->getFrequency()) {

            case Schedule::ASAP:
                
                
                
                $ready = true;
                break;

            case Schedule::FIXED_TIME:

                $ready = $this->isReadyFixedTime($schedule, $now);
                break;

            case Schedule::HOURLY:
                break;

            case Schedule::WEEKLY:
                break;

            case Schedule::DAILY:
                break;

        }

        $this->logForJob($job, "\"{$job->getScheduleOriginal()}\" - " . ($ready ? "will be added to queue..." : "not ready for scheduling"));

        return $ready;

    }

    /**
     * @param Schedule $schedule
     * @param $now
     * @return bool
     * @throws \Exception
     */
    private function isReadyFixedTime(Schedule $schedule, $now)
    {

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

    /**
     * @param JobDefinition $job
     * @param $message
     * @param int $type
     */
    private function logForJob(JobDefinition $job, $message, $type = Logger::INFO)
    {

        $message = "Job ID: {$job->getId()} - " . $message;
        $this->log->log($type, $message);
    }

}