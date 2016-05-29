<?php

namespace Tests\Api;

use Autoq\Services\ApiHelper;
use GuzzleHttp\Client;
use Phalcon\Config;
use Phalcon\Di;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class JobControllerTest extends \PHPUnit_Framework_TestCase
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
     * @var $client Client
     */
    protected $client;

    /**
     * Setup for whole class
     */
    public static function setUpBeforeClass()
    {

        self::$di = Di::getDefault();
        self::$config = self::$di->get('config');

    }

    /**
     * Setup for each test
     */
    protected function setUp()
    {
        $this->client = new Client([
            'base_uri' => 'http://api'
        ]);
    }

    /**
     * Teardown on each test
     */
    protected function tearDown()
    {
        $this->client = null;
    }


    public function testGetJobs()
    {
        $response = $this->client->get('/jobs/1');
    }


    /**
     * Test Post of example job 1
     * @throws \Exception
     */
    public function testPostJobExample1()
    {
        $resource = $this->getDataFileResource('example_job_1.yaml');

        $rawResponse = $this->client->request('POST', '/jobs/', ['body' => $resource]);

        $response = json_decode($rawResponse->getBody());

        $this->assertObjectHasAttribute('status', $response);
        $this->assertEquals('success', $response->status);

        $this->assertObjectHasAttribute('data', $response);

        $data = $response->data;

        $this->assertTrue(is_numeric($data->id) && (int)$data->id > 0);

        $sourceYaml = $this->loadDataFileAsYaml('example_job_1.yaml');

        $this->assertEquals($sourceYaml['name'], $data->name);
        $this->assertEquals($sourceYaml['query'], $data->query);

        $this->assertNotNull($data->created);
        $this->assertNull($data->updated);

        $this->assertEquals(count($sourceYaml['outputs']), count($data->outputs));

        //Outputs are currently returned unchanged - checks this is the case
        $index = 0;
        foreach($sourceYaml['outputs'] as $output) {
            $this->assertArraySubset((array)$data->outputs[$index++], $output);
        }
    }

    /**
     * Read into memory a YAML file
     * @param $filename
     * @return bool|mixed
     */
    private function loadDataFileAsYaml($filename)
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
    private function getDataFileResource($filename)
    {

        $filepath = __DIR__ . "/data/$filename";

        if (($fh = fopen($filepath, 'r')) === false) {
            throw new \Exception("Could not open: $filepath");
        }

        return $fh;
    }

}