<?php

namespace Autoq\Tests\Api;

use Autoq\Tests\Autoq_TestCase;
use GuzzleHttp\Client;

class MiscApiTest extends Autoq_TestCase
{

    /**
     * @var $client Client
     */
    protected $client;

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


    /**
     * Test the root url
     */
    public function testRoot()
    {

        $rawResponse = $this->client->request('GET', '/', ['http_errors' => false]);
        $response = json_decode($rawResponse->getBody());

        $this->assertEquals('error', $response->status);
        $this->assertEquals('Requested url not found', $response->reason);

    }

}