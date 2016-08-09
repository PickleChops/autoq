<?php

namespace Autoq\Services\JobProcessor;

use Autoq\Services\DbCredentialsService;
use Autoq\Services\JobProcessor\ItemProcessors\JobConnectionProcessor;
use Autoq\Services\JobProcessor\ItemProcessors\JobNameProcessor;
use Autoq\Services\JobProcessor\ItemProcessors\JobScheduleProcessor;
use Phalcon\Validation\Message\Group;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class JobDefinitionProcessor
{
    /**
     * @var $messages Group
     */
    private $messages;

    /**
     * @var $definition mixed
     */
    private $definition;
    /**
     * @var DbCredentialsService
     */
    private $dbCredentialsService;


    /**
     * JobDefinitionProcessor constructor.
     * @param DbCredentialsService $dbCredentialsService
     */
    public function __construct(DbCredentialsService $dbCredentialsService)
    {
         $this->dbCredentialsService = $dbCredentialsService;

        $this->messages = new Group();
    }

    /**
     * Given raw input from user attempt to validate as a job defintion
     * @param $rawDefinition
     * @return array
     */
    public function processJobDefiniton($rawDefinition)
    {

        if ($rawDefinition == "") {
            $this->messages->appendMessage(JobProcessorErrors::asMessageObject(JobProcessorErrors::MSG_NO_DEFINTION));
            return false;
        }

        if (($parsedYaml = $this->parseYaml($rawDefinition, $message)) === false) {
            $message = JobProcessorErrors::asMessageObject($message);
            $message->setCode(JobProcessorErrors::TYPE_YAML_ERROR);
            $this->messages->appendMessage($message);
            return false;
        }

        if ($this->processParsedYaml($parsedYaml) === false) {
            return false;
        }

        $this->definition = $parsedYaml;

        return true;

    }

    /**
     * Validate the parsed YAML
     * @param $data
     * @return Group
     */
    private function processParsedYaml($data)
    {

        //Name

        $nameProcessor = new JobNameProcessor($data['name'], $this->dbCredentialsService);

        if (!$nameProcessor->getIsValid()) {
            $this->appendMessages($nameProcessor->getMessages());
        }

        //Connection

        $connectionProcessor = new JobConnectionProcessor($data['connection'], $this->dbCredentialsService);

        if (!$connectionProcessor->getIsValid()) {
            $this->appendMessages($connectionProcessor->getMessages());
        }

        //Schedule

        $scheduleProcessor = new JobScheduleProcessor($data['schedule'], $this->dbCredentialsService);

        if (!$scheduleProcessor->getIsValid()) {
            $this->appendMessages($scheduleProcessor->getMessages());
        }

        return $this->messages->count() == 0;

    }


    /**
     * PArse YAML to php
     * @param $rawDef
     * @param $message
     * @return bool|Parser
     */
    private function parseYaml($rawDef, &$message = null)
    {
        $data = false;

        try {

            $yaml = new Parser();
            $data = $yaml->parse($rawDef);

        } catch (ParseException $e) {
            $message = $e->getMessage();
        }

        return $data;
    }

    /**
     * If validation passed this will return the job defintion
     * @return mixed
     */
    public function getValidatedDefinition()
    {
        return $this->definition;
    }

    /**
     * Get first error from validation
     * @return string
     */
    public function getFirstError()
    {

        $message = "";

        if ($this->messages->count()) {
            $this->messages->rewind();
            $msgObj = $this->messages->current();
            $message = $msgObj->getMessage();
        }

        return $message;
    }

    /**
     * @return Group
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param Group $messages
     */
    private function appendMessages(Group $messages)
    {

        foreach ($messages as $message) {
            $this->messages->appendMessage($message);
        }

    }

}