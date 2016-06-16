<?php

namespace Autoq\Tests;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class Autoq_TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $di Di
     */
    protected static $di;

    /**
     * @var $config Config
     */
    protected static $config;

    /**
     * @var $connection Mysql
     */
    protected static $dbConnection = null;

    const BCKUP_TABLE_PREFIX = '__';

    /**
     * Setup for whole class
     */
    public static function setUpBeforeClass()
    {
        self::$di = Di::getDefault();
        self::$config = self::$di->get('config');
    }

    /**
     * Read into memory a YAML file
     * @param $filename
     * @return bool|mixed
     */
    protected function loadDataFileAsYaml($filename)
    {

        $filepath = $this->getTestDataResourceFilePath($filename);

        $data = false;

        try {

            $yaml = new Parser();
            $data = $yaml->parse(file_get_contents($filepath));

        } catch (ParseException $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        return $data;
    }

    /**
     * Get file resource for a test data file
     * @param $filename
     * @return resource
     * @throws \Exception
     */
    protected function getDataFileResource($filename)
    {
        $filepath = $this->getTestDataResourceFilePath($filename);

        if (($fh = fopen($filepath, 'r')) === false) {
            throw new \Exception("Could not open: $filepath");
        }

        return $fh;
    }

    /**
     * @param $filename
     * @return string
     */
    protected function getTestDataResourceFilePath($filename)
    {
        return __DIR__ . "/_data/$filename";
    }

    /**
     * Get a shared DB connection
     * @return Mysql
     */
    protected static function getDBConnection()
    {
        if(self::$dbConnection === null) {
            $dBConnectionMgr = self::$di->get('dBConnectionMgr');
            self::$dbConnection = $dBConnectionMgr->getConnection('mysql');
        }

        return self::$dbConnection;
    }

    /**
     * Note the recreation of the autoinc, a CREATE TABLE loses keys so this is a workaround for now
     * @param $table
     * @param string $autoinc
     * @return string
     */
    protected static function backupTable($table, $autoinc = 'id')
    {
        $backupTable = self::getBackupTableName($table);
        self::getDBConnection()->execute("DROP TABLE IF EXISTS {$backupTable}");
        self::getDBConnection()->execute("CREATE TABLE {$backupTable} ({$autoinc} INT AUTO_INCREMENT PRIMARY KEY) AS SELECT * FROM {$table}");
    }

    /**
     * @param $table
     * @return string
     */
    protected static function truncateTable($table)
    {
        self::getDBConnection()->execute("TRUNCATE TABLE {$table}");

    }

    /**
     * @param $table
     */
    protected static function backupAndClearTableForTesting($table) {

        self::backupTable($table);
        self::truncateTable($table);

    }

    /**
     * @param $table
     */
    protected static function restoreTable($table) {
        $backupTable = self::getBackupTableName($table);
        self::getDBConnection()->execute("DROP TABLE IF EXISTS {$table}");
        self::getDBConnection()->execute("RENAME TABLE {$backupTable} TO {$table}");
    }

    /**
     * @param $table
     * @return string
     */
    protected static function getBackupTableName($table) {
        return self::BCKUP_TABLE_PREFIX.$table;
    }
}