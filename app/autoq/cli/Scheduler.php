<?php

namespace Autoq\Cli;

use Autoq\Data\Jobs\JobsRepository;
use Autoq\Services\DatabaseConnections;
use Phalcon\Config;
use Phalcon\Di;

class Scheduler
{

    /**
     * @var $di Di
     */
    protected $di;

    /**
     * @var $dBConnections DatabaseConnections
     */
    protected $dBConnectionService;

    /**
     * @var $config Config
     */
    protected $config;

    /**
     * @var $jobRepo JobsRepository
     */
    protected $jobsRepo;

    /**
     * Scheduler constructor.
     * @param Di $di
     * @param array $argv
     */
    public function __construct(Di $di, Array $argv)
    {
        $this->di = $di;
        $this->config = $this->di->get('config');
        $this->dBConnectionService = $this->di->get('dBConnectionService');
        $this->jobsRepo = $this->di->get(JobsRepository::class, [$di]);
        
        $this->main();

    }

    /**
     * Off we go
     */
    protected function main() {
        
        
        $jobs = $this->jobsRepo->getAll();

        var_dump($jobs);
        
        
        
        //Determine if job is ready to be scheduled
        
        
        //If ready add to queue
        
    
    }
    
    
    

}