<?php

namespace Autoq\Cli;

use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Jobs\JobsRepository;
use Autoq\Data\Queue\QueueControl;
use Phalcon\Config;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class Sender implements CliTask
{

    protected $config;
    protected $log;
    protected $jobsRepo;
    protected $queueControl;

    protected $args;

    protected $timeHorizon;

    /**
     * Sender constructor.
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
        $this->log->info("Runner started");

        while (true) {
            
            //Get FETCH_COMPLETED records
            
            //Inspect outputs
            
            //Perform outputs
            
            //Mark records as COMPLETED


            sleep($this->config->app->sender_sleep);
        }

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