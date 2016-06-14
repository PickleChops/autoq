<?php

namespace Autoq\Cli;

use Autoq\Cli\Lib\JobScheduler;
use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Jobs\JobsRepository;
use Autoq\Data\Queue\QueueRepository;
use Phalcon\Config;
use Phalcon\Di;

class Scheduler extends CliBase
{

    /**
     * @var $jobRepo JobsRepository
     */
    protected $jobsRepo;

    /**
     * @var $queueRepo QueueRepository
     */
    protected $queueRepo;

    /**
     * Scheduler constructor.
     * @param Di $di
     * @param array $argv
     */
    public function __construct(Di $di, Array $argv)
    {
        parent::__construct($di, $argv);
        
        $this->log->info("Scheduler started");

        $this->jobsRepo = $this->di->get(JobsRepository::class, [$di]);
        $this->queueRepo = $this->di->get(QueueRepository::class, [$di]);


        //Poll database looking for new jobs to schedule
        while(true) {
            $this->main();
            sleep($this->config->app->scheduler_sleep);
        }
        
    }

    /**
     * Off we go
     */
    public function main()
    {
        $jobScheduler = new JobScheduler($this->config, $this->log);

        //Get the currently defined jobs

        $this->log->debug("Looking for job definitions");
        
        /**
         * @var $jobs JobDefinition[]
         */
        $jobs = $this->jobsRepo->getAll();
        
        $this->log->debug(count($jobs) . " job definitions found");

        // Loop through the jobs looking for jobs that are ready to schedule

        foreach ($jobs as $job) {

            $schedule = $job->getSchedule();

            if ($jobScheduler->isJobReadyToSchedule($schedule)) {

                $queueItem = [];

                $this->queueRepo->save($queueItem);

            }

        }

    }

}