<?php

/**
 * Bootstrap and start a cli process
 */
class CLI
{
    /**
     * @var $di \Phalcon\Di
     */
    protected $di;

    /**
     * CLI constructor.
     * @param $argv
     */
    public function __construct($argv)
    {
        //Start up code
        $this->di = require __DIR__ . "/bootstrap/cliStart.php";

        //Dispatch to relevant task
        global $argv;
        $this->dispatch($argv);
    }

    /**
     * Check the cli task we want to run and run it
     * @param $argv
     */
    private function dispatch($argv)
    {

        if (count($argv) == 1) {
            $this->exitWithMessage("No cli task specified");
        }

        $processToRun = $argv[1];

        $argsForTask = array_slice($argv, 2);


        if ($this->di->has($processToRun)) {

            /**
             * Get the task from the DI container
             * @var $task \Autoq\Cli\CliTask
             */
            $task = $this->di->get($processToRun);

            if (in_array('Autoq\Cli\CliTask', class_implements($task))) {
                
                //And run the Cli task
                $task->main($argsForTask);

            } else {
                $this->exitWithMessage('A Cli task must implement the \Autoq\Cli\CliTask interface');
            }

        } else {
            $this->exitWithMessage("Unable to find cli task: $processToRun");
        }
    }

    /**
     * @param $message
     * @param int $exitCode
     */
    protected
    function exitWithMessage($message, $exitCode = 1)
    {
        echo $message . PHP_EOL;
        exit($exitCode);
    }
}

new CLI($argv);













