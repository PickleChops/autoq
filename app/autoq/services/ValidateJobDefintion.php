<?php

namespace Autoq\Services;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

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

    private $errorMsg;
    

    private $def;


    /**
     * @param $rawDef
     * @return array
     */
    public function validateDefiniton($rawDef)
    {

        if ($rawDef == "") {
            $this->errorMsg = $this->msgText[self::MSG_NO_DEFINTION];
            return false;
        }

        if (($parsedYaml = $this->parseYaml($rawDef, $message)) === false) {
            $this->errorMsg = $message;
            return false;
        }

        if ($this->validateParsedYaml($parsedYaml, $message) === false) {
            $this->errorMsg = $message;
            return false;
        }

        $this->def = $parsedYaml;
        
        return true;

    }


    /**
     * Validate the data from the Yaml file
     * @param $data
     * @param null $message
     * @return Validation\Message\Group
     */
    public function validateParsedYaml($data, &$message = null) {
        
               
        $validation = new Validation();

        //Check basics for job
        $validation->add('name', new PresenceOf(['messsage' => "A job name must be specified"]));
        $validation->add('connection', new PresenceOf(['messsage' => "A connection must be specified"]));
        $validation->add('schedule', new PresenceOf(['messsage' => "A schedule must be specified"]));
        $validation->add('query', new PresenceOf(['messsage' => "A query must be specified"]));

        $messages = $validation->validate($data);

        //@TODO Add the rest of the validation once model is in - this is also an easy test targer

        if(count($messages)) {
            $message = $messages[0]->getMessage();
            return false;
        } else {
            $message = null;
            return true;
        }
    }

    /**
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
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * @return mixed
     */
    public function getDefAsYaml()
    {
        return $this->def;
    }
    
    

}