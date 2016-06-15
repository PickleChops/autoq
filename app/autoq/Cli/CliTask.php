<?php

namespace Autoq\Cli;

use Phalcon\Di;

interface CliTask
{
    public function main(Array $args = []);
}