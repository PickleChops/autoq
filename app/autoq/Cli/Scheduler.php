<?php

namespace Autoq\Cli;

use Autoq\Cli\Lib\JobScheduler;
use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Jobs\JobsRepository;
use Autoq\Data\Queue\QueueRepository;
use Autoq\Lib\ScheduleParser\Schedule;
use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Logger\Adapter\Stream;

class Scheduler implements CliTask
{

    protected $config;
    protected $log;
    protected $jobsRepo;
    protected $queueRepo;

    protected $args;

    protected $timeHorizon;

    /**
     * Scheduler constructor.
     * @param Config $config
     * @param Stream $log
     * @param JobsRepository $jobRepo
     * @param QueueRepository $queueRepo
     */
    public function __construct(Config $config, Stream $log, JobsRepository $jobRepo, QueueRepository $queueRepo)
    {
        $this->config = $config;
        $this->log = $log;
        $this->jobsRepo = $jobRepo;
        $this->queueRepo = $queueRepo;
    }

    /**
     * Off we go
     * @param array $args
     */
    public function main(Array $args = [])
    {
        $this->log->info("Scheduler started");

        $jobScheduler = new JobScheduler($this->config, $this->log);

        //Poll database looking for new jobs to schedule
        while (true) {

            //Get the currently defined jobs
            $this->log->debug("Looking for job definitions");

            /**
             * @var $jobs JobDefinition[]
             */
            $jobs = $this->jobsRepo->getAll();

            $this->log->debug(count($jobs) . " job definitions found");

            // Loop through the jobs looking for jobs that are ready to schedule

            foreach ($jobs as $job) {

                if ($jobScheduler->isJobReadyToSchedule($job)) {


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