<?php

namespace Autoq\Cli;

use Phalcon\Di;

interface CliTask
{
    public function __construct(Di $di, Array $args = []);
    public function main();
}