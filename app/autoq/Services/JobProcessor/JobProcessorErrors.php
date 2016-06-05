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
    const MSG_CONNECTION_NOT_DEFAULT = 8;
    const MSG_JOB_NAME_TOO_LONG = 9;

    static private $msgText = [

        self::MSG_UNKNOWN_ERROR => "Unknown error code",
        self::MSG_NO_DEFINTION => "The job definition is empty",
        self::MSG_NO_JOB_NAME => 'A job name must be provided',
        self::MSG_NO_QUERY => "No job query provided",
        self::MSG_NO_SCHEDULE => "No schedule provided",
        self::MSG_NO_OUTPUT => "No output(s) provided",
        self::MSG_NO_CONNECTION => "No connection provided",
        self::MSG_CONNECTION_NOT_DEFAULT => "Only the default connection is currently supported'",
        self::MSG_JOB_NAME_TOO_LONG => "Job names must be 255 characters or less"
    ];

    /**
     * Helper Factory for messages
     * @param $codeOrMsgString
     * @param null $field
     * @param null $type
     * @return Message
     */
    public static function asMessageObject($codeOrMsgString, $field = null, $type = null)
    {

        if (is_int($codeOrMsgString)) {
            $code = $codeOrMsgString;

            if (!array_key_exists($code, self::$msgText)) {
                $code = self::MSG_UNKNOWN_ERROR;
            }

            $messageString = self::$msgText[$code];

        } else {
            $messageString = (string)$codeOrMsgString;
            $code = null;
        }

        return new Message(
            $messageString,
            $field,
            $type,
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