<?php

namespace Autoq\Cli;

use Autoq\Data\Queue\QueueControl;
use Autoq\Data\Queue\FlowControl;
use Autoq\Data\Queue\QueueItem;
use Autoq\Services\DbConnectionMgr;
use Autoq\Services\DbCredentialsService;
use Phalcon\Config;
use Phalcon\Db\Adapter;
use Phalcon\Db\ResultInterface;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class Admin implements CliTask
{

    protected $config;
    protected $log;
    protected $queueControl;
    private $dbConnectionMgr;

    /**
     * Runner constructor.
     * @param Config $config
     * @param DbConnectionMgr $dbConnectionMgr
     */
    public function __construct(Config $config, DbConnectionMgr $dbConnectionMgr)
    {
        $this->config = $config;
        $this->dbConnectionMgr = $dbConnectionMgr;
    }

    /**
     * Off we go
     * @param array $args
     */
    public function main(Array $args = [])
    {
        if (count($args) != 2) {
            $this->fatal("Usage: Admin <fullname> <email>");
        }

        $fullname = trim($args[0]);
        $email = trim($args[1]);

        //Some simple validation on input

        if(!preg_match("/^[a-zA-z\\- ].+$/i", $fullname)) {
            $this->fatal("Name format not allowed");
        }

        if(!preg_match("/^\\S+@\\S+$/i", $email)) {
            $this->fatal("Email format not recognized");
        }

        //Generate the api key

        $apiKey = bin2hex(openssl_random_pseudo_bytes(40));

        //Write the user to the database and output details
        try {
            if (($connection = $this->dbConnectionMgr->getConnection('mysql')) !== null) {

                $connection->insertAsDict('api_access_keys', ['fullname' => $fullname, 'email' => $email, 'api_key' => $apiKey]);

                $this->output("Api key genererated");
                $this->output("-------------------");
                $this->output("Full name : $fullname");
                $this->output("Email     : $email");
                $this->output("Api Key   : $apiKey");

            } else {
                $this->output("Unable to connect to Autoq database");
            }

        } catch (\Exception $e) {
            $this->fatal("Something went wrong: " . $e->getMessage());
        }
    }

    /**
     * @param $message
     */
    protected function output($message)
    {
        echo $message . PHP_EOL;
    }

    /**
     * @param $message
     */
    protected function fatal($message)
    {
        $this->output($message);
        exit(1);
    }

}