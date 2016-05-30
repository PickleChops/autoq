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

}