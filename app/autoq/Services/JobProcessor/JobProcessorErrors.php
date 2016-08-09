<?php

namespace Autoq\Services\JobProcessor;

use Phalcon\Validation\Message;

class JobProcessorErrors
{
    const TYPE_YAML_ERROR = 1;

    const MSG_UNKNOWN_ERROR = 1;
    const MSG_NO_DEFINTION = 2;
    const MSG_NO_JOB_NAME = 3;
    const MSG_NO_QUERY = 4;
    const MSG_NO_SCHEDULE = 5;
    const MSG_NO_OUTPUT = 6;
    const MSG_NO_CONNECTION = 7;
    const MSG_CONNECTION_NOT_FOUND = 8;
    const MSG_FIELD_DATA_TOO_LONG = 9;
    const MSG_UNABLE_TO_PARSE_SCHEDULE = 10;
 
    static private $msgText = [

        self::MSG_UNKNOWN_ERROR => "Unknown error code",
        self::MSG_NO_DEFINTION => "The job definition is empty",
        self::MSG_NO_JOB_NAME => 'A job name must be provided',
        self::MSG_NO_QUERY => "No job query provided",
        self::MSG_NO_SCHEDULE => "No schedule provided",
        self::MSG_NO_OUTPUT => "No output(s) provided",
        self::MSG_NO_CONNECTION => "No connection provided",
        self::MSG_CONNECTION_NOT_FOUND => "Connection '%s' not found, are you sure it is set up?",
        self::MSG_FIELD_DATA_TOO_LONG => "Field data must be 255 characters or less",
        self::MSG_UNABLE_TO_PARSE_SCHEDULE => "Unable to parse the schedule"
    ];

    /**
     * Helper Factory for messages
     * @param $codeOrMsgString
     * @param array $params
     * @return Message
     */
    public static function asMessageObject($codeOrMsgString, $params = [])
    {

        if (is_int($codeOrMsgString)) {
            $code = $codeOrMsgString;

            if (!array_key_exists($code, self::$msgText)) {
                $code = self::MSG_UNKNOWN_ERROR;
            }

            $messageString = vsprintf(self::$msgText[$code], $params);

        } else {
            $messageString = vsprintf((string)$codeOrMsgString, $params);
            $code = null;
        }

        return new Message(
            $messageString,
            null,
            null,
            $code
        );
    }

    /**
     * @param $code
     * @return mixed
     */
    public static function errorString($code)
    {
        return self::$msgText[$code];
    }
}