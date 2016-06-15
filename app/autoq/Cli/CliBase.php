<?php

namespace Autoq\Cli;

use Autoq\Services\DatabaseConnections;
use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Logger\Adapter\Stream;

abstract class CliBase implements CliTask
{

    /**
     * @var $di Di
     */
    protected $di;

    /**
     * @var $args []
     */
    protected $args;


    /**
     * @var $dBConnections DatabaseConnections
     */
    protected $dBConnectionService;

    /**
     * @var $config Config
     */
    protected $config;

    /**
     * @var $log Stream
     */
    protected $log;
    
    /**
     * Scheduler constructor.
     * @param Di $di
     * @param array $args
     */
    public function __construct(Di $di, Array $args = [])
    {
        $this->di = $di;
        $this->args = $args;
        $this->config = $this->di->get('config');
        $this->log = $this->di->get('log');
        $this->dBConnectionService = $this->di->get('dBConnectionService');
    }


    /**
     * Override for main code of a Cli task
     * @return mixed
     */
    abstract public function main();
    
  

}