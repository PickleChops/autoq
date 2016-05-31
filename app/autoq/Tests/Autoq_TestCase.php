<?php

namespace Autoq\Tests;

use Phalcon\Config;
use Phalcon\Di;

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

        $filepath = __DIR__ . "/data/$filename";

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

        $filepath = __DIR__ . "/data/$filename";

        if (($fh = fopen($filepath, 'r')) === false) {
            throw new \Exception("Could not open: $filepath");
        }

        return $fh;
    }
}