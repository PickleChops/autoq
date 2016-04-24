<?php

//namespace Api\Models;

use Phalcon\Config;
use Phalcon\Db;
use Phalcon\Db\Exception;
use Phalcon\Db\Adapter\Pdo\Mysql as MysqlConnection;
use Phalcon\Di;

class JobModel
{

    protected $di;

    /**
     * @param Di $di
     */
    public function __construct(Di $di)
    {

        /**
         * @var $config Config
         */
        $config = $di->get('config');

        try {

            $connection = new MysqlConnection(array(
                'host' => $config['database']['host'],
                'username' => $config['database']['username'],
                'password' => $config['database']['password'],
                'dbname' => $config['database']['dbname'],
                'port' => $config['database']['port'],
            ));
            
        
            $result = $connection->query("SELECT * FROM robots LIMIT 5");
            $result->setFetchMode(Db::FETCH_NUM);
            while ($robot = $result->fetch()) {
                print_r($robot);
            }

        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }

}