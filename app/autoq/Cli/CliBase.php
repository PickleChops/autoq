<?php

namespace Autoq\Cli;

use Autoq\Services\DatabaseConnections;
use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Logger\Adapter\Stream;

abstract class CliBase
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
     * @var $log Stream
     */
    protected $log;


    /**
     * Scheduler constructor.
     * @param Di $di
     * @param array $argv
     */
    public function __construct(Di $di, Array $argv)
    {
        $this->di = $di;
        $this->config = $this->di->get('config');
        $this->log = $this->di->get('log');
        $this->dBConnectionService = $this->di->get('dBConnectionService');
    }
    
}