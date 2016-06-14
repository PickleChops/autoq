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

        $this->jobsRepo = $this->di->get(JobsRepository::class, [$di]);
        $this->queueRepo = $this->di->get(QueueRepository::class, [$di]);

        $this->main();

    }

    /**
     * Off we go
     */
    public function main()
    {
        $jobScheduler = new JobScheduler();

        //Get the currently defined jobs

        /**
         * @var $jobs JobDefinition[]
         */
        $jobs = $this->jobsRepo->getAll();

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