<?php

namespace Autoq\Services\JobProcessor;

use Autoq\Services\JobProcessor\ItemProcessors\JobConnectionProcessor;
use Autoq\Services\JobProcessor\ItemProcessors\JobNameProcessor;
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
     * JobDefinitionProcessor constructor.
     */
    public function __construct()
    {
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
            $this->messages->appendMessage(JobProcessorErrors::asMessageObject($message, null, JobProcessorErrors::TYPE_YAML_ERROR));
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

        $nameProcessor = new JobNameProcessor($data['name']);

        if (!$nameProcessor->getIsValid()) {
            $this->appendMessages($nameProcessor->getMessages());
        }

        $connectionProcessor = new JobConnectionProcessor($data['connection']);

        if (!$connectionProcessor->getIsValid()) {
            $this->appendMessages($connectionProcessor->getMessages());
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