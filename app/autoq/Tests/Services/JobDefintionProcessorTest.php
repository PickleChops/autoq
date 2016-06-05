<?php

namespace Autoq\Tests\Services;
use Autoq\Services\JobProcessor\JobDefinitionProcessor;
use Autoq\Services\JobProcessor\JobProcessorErrors;
use Autoq\Tests\Autoq_TestCase;
use Phalcon\Validation\Message;

class ValidateJobDefintionTest extends Autoq_TestCase
{

    public function testMissingJobName()
    {
        $input = file_get_contents($this->getTestDataResourceFilePath('example_job_no_job_name.yaml'));

        $processor = new JobDefinitionProcessor();

        $valid = $processor->processJobDefiniton($input);

        $this->assertEquals(false, $valid);

        $messages = $processor->getMessages()->filter('name');

        $this->assertTrue(count($messages) == 1);

        /**
         * @var $message Message
         */
        $message = reset($messages);

        $this->assertTrue($message->getMessage() == JobProcessorErrors::errorString(JobProcessorErrors::MSG_NO_JOB_NAME));

    }

    public function testInvalidConnection()
    {
        $input = file_get_contents($this->getTestDataResourceFilePath('example_job_invalid_connection.yaml'));

        $validator = new JobDefinitionProcessor();

        $valid = $validator->processJobDefiniton($input);

        $this->assertEquals(false, $valid);

        $messages = $validator->getMessages()->filter('connection');

        $this->assertTrue(count($messages) == 1);

        /**
         * @var $message Message
         */
        $message = reset($messages);

        $this->assertTrue($message->getMessage() == JobProcessorErrors::errorString(JobProcessorErrors::MSG_CONNECTION_NOT_DEFAULT));

    }
}