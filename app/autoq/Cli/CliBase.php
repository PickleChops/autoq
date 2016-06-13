<?php

namespace Autoq\Cli;

use Autoq\Services\DatabaseConnections;
use Phalcon\Config;
use Phalcon\Di;

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
     * Scheduler constructor.
     * @param Di $di
     * @param array $argv
     */
    public function __construct(Di $di, Array $argv)
    {
        $this->di = $di;
        $this->config = $this->di->get('config');
        $this->dBConnectionService = $this->di->get('dBConnectionService');
    }
    
}