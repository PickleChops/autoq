<?php

namespace Api\Services;


use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class ValidateJobDefintion
{

    const MSG_NO_DEFINTION = 1;
    const MSG_NO_QUERY = 2;
    const MSG_NO_SCHEDULE = 3;
    const MSG_NO_OUTPUT = 4;
    const MSG_NO_CONNECTION = 5;

    private $msgText = [

        self::MSG_NO_DEFINTION => "The job definition is empty",
        self::MSG_NO_QUERY => "No job query provided",
        self::MSG_NO_SCHEDULE => "No schedule provided",
        self::MSG_NO_OUTPUT => "No output(s) provided",
        self::MSG_NO_CONNECTION => "No connection provided"
    ];

    private $response = [];

    private $def;


    /**
     * @param $rawDef
     * @return array
     */
    public function validateDefiniton($rawDef)
    {

        if ($rawDef == "") {
            return $this->addMsgToResponse(self::MSG_NO_DEFINTION);
        }

        if(($this->def = $this->parseYaml($rawDef, $message)) === false) {
            return $this->addMsgToResponse($message);
        }

        

    }


    /**
     * @param $keyOrMessage
     * @return array
     */
    private function addMsgToResponse($keyOrMessage)
    {
        $this->response[] = array_key_exists($keyOrMessage, $this->msgText) ? $this->msgText[$keyOrMessage] : $keyOrMessage;

        return $this->response;
    }


    /**
     * @param $rawDef
     * @param $message
     * @return bool|Parser
     */
    private function parseYaml($rawDef, &$message = null)
    {
        $yaml = false;

        try {

            $yaml = new Parser();
            $yaml->parse($rawDef);

        } catch (ParseException $e) {
            $message = $e->getMessage();
        }

        return $yaml;
    }

}