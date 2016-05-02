<?php
/**
 * User: bstratton
 * Date: 06/07/15
 * Time: 10:48
 */

namespace Lib\Debug;


class DebugError implements DebugEventInterface
{
    private $code = null;
    private $message = null;
    private $file = null;
    private $line = null;
    private $trace = [];
    private $metadata = [];

    /**
     * @param $message
     */
    public function __construct($message) {
        $this->message = $message;
    }

    /**
     * @param null $code
     * @return DebugError
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param null $message
     * @return DebugError
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param null $file
     * @return DebugError
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @param null $line
     * @return DebugError
     */
    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }

    /**
     * @param array|null $trace
     * @return DebugError
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;
        return $this;
    }


    /**
     * @return number|null
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * @return string|null
     */
    public function getFile()
    {
        return $this->file;
    }


    /**
     * @return number|null
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return array
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }



}