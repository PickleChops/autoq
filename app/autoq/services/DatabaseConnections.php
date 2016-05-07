<?php

namespace Autoq\Services;


use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Di;
use Phalcon\Logger\Adapter\Stream;

class DatabaseConnections
{
    const DEFAULT_RETRIES = 3;
    const DEFAULT_WAIT = 10;

    private $di;


    private $defaultConfigParams = [

        'host' => '',
        'username' => '',
        'password' => '',
        'dbname' => '',
        'port' => ''

    ];

    /**
     * @var $log Stream
     */
    private $log;

    /**
     * @param $di Di
     */
    public function __construct(Di $di)
    {
        $this->di = $di;
        $this->log = $this->di->get('log');
    }

    /**
     * @param $dBConfig
     * @param int $retries
     * @param int $wait
     * @return mixed
     */
    public function getConnection($dBConfig, $retries = self::DEFAULT_RETRIES, $wait = self::DEFAULT_WAIT)
    {


        $connection = null;
        $attempts = 1;

        $config = array_merge($this->defaultConfigParams, (array)$dBConfig);

        $adapter = $config['adapter'];

        while ($attempts <= $retries) {

            try {

                $connection = new $adapter(array(
                    'host' => $config['host'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'dbname' => $config['dbname'],
                    'port' => $config['port'],
                ));

            } catch (\PDOException $e) {
                $this->log->warning("Connection failure attempt $attempts of $retries to host: {$config['host']} database: {$config['dbname']}");
                $attempts++;
            }

            if ($connection instanceof $adapter) {
                break;
            } elseif ($attempts < $retries) {
                sleep($wait);
            } elseif ($attempts == $retries) {
                $this->log->error("$attempts connection attempts to host: {$config['host']} database: {$config['dbname']} failed");
            }
        }

        return $connection;
    }

}