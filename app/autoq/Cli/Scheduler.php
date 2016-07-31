<?php

namespace Autoq\Cli;

use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Jobs\JobsRepository;
use Autoq\Data\Queue\QueueControl;
use Autoq\Lib\ScheduleParser\Schedule;
use Autoq\Lib\Time\Time;
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

            $time = new Time(time());

            /**
             * @var $jobs JobDefinition[]
             */
            $jobs = $this->jobsRepo->getAll();


            $this->log->info("Checking the schedule for " . count($jobs) . " job definitions");

            // Loop through the jobs looking for jobs that are ready to schedule

            foreach ($jobs as $job) {

                $schedule = $job->getSchedule();

                //Ignore scehdules that are unable to provide a next event. e.g. Schedule::NONE
                if (($nextEventTs = $schedule->getNextEventTs($time)) !== false) {

                    $nextEventFriendly = date('Y-m-d H:i:s', $nextEventTs);
                    $currentTimeFriendly = date('Y-m-d H:i:s', $time->getTimestamp()); 
                        

                    if ($nextEventTs > $time->getTimestamp()) {

                        //The job is not ready to run
                        $this->logForJob($job, "Not scheduled: " . $nextEventFriendly . ' is later than now: ' . $currentTimeFriendly);

                    } else {

                        //Ok so job meets criteria of being ready

                        $queueID = $this->queueControl->addNew($job);

                        $this->logForJob($job, "Scheduled: " . $nextEventFriendly . ' is less than or equal to now: ' . $currentTimeFriendly);
                        $this->logForJob($job, "Added to work queue with ID: $queueID");

                        //To avoid loops with ASAP events the schedule is returned to NONE once initial scheduling has occurred
                        if ($schedule->getFrequency() == Schedule::ASAP) {
                            $schedule->setFrequency(Schedule::NONE);
                            $job->setScheduleOriginal($schedule->getReadableFrequency());
                            $this->jobsRepo->update($job->getId(), $job->convertToOrginalDefinition());
                        }

                    }
                } else {
                    $this->logForJob($job, "No next event time provided for schedule: " . $schedule->getReadableFrequency());
                }
            }

            sleep($this->config['app']['scheduler_sleep']);
        }
    }


    /**
     * @return mixed
     */
    public
    function getTimeHorizon()
    {
        return $this->timeHorizon;
    }

    /**
     * @param mixed $timeHorizon
     */
    public
    function setTimeHorizon($timeHorizon)
    {
        $this->timeHorizon = $timeHorizon;
    }

    /**
     * @param JobDefinition $job
     * @param $message
     * @param int $type
     */
    private
    function logForJob(JobDefinition $job, $message, $type = Logger::INFO)
    {

        $message = "Job ID: {$job->getId()} - " . $message;
        $this->log->log($type, $message);
    }

}