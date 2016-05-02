<?php

/**
 * Bootstrap and start a cli process
 */
class CLI
{
    protected $di;

    /**
     * CLI constructor.
     * @param $argv
     */
    public function __construct($argv)
    {
        //Start up code
        $this->di = require __DIR__ . "/bootstrap/cliStart.php";

        //Setup error/exception handler
        \Lib\Debug\Debug::initialize(true, STDOUT);

        //Dispatch to relevant task
        global $argv;
        $this->dispatch($argv);
    }

    /**
     * @param $argv
     */
    private function dispatch($argv)
    {

        if (count($argv) == 1) {
            $this->exitWithMessage("No cli task specified");
        }

        $processToRun = $argv[1];

        $argsForTask = array_slice($argv, 2);
        
        $taskWithNS = "CLI\\$processToRun";

        if (class_exists($taskWithNS)) {
            new $taskWithNS($this->di, $argsForTask);
        } else {
            $this->exitWithMessage("Unable to find cli task: $processToRun");
        }
    }

    /**
     * @param $message
     * @param int $exitCode
     */
    protected function exitWithMessage($message, $exitCode = 1)
    {
        echo $message . PHP_EOL;
        exit($exitCode);
    }
}

new CLI($argv);













