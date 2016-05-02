<?php
/**
 * User: bstratton
 * Date: 06/07/15
 * Time: 10:48
 */

namespace Lib\Debug;


class DebugException implements DebugEventInterface
{
    private $code = null;
    private $message = null;
    private $file = null;
    private $line = null;
    private $trace = [];
    private $originalException = null;
    private $metadata = [];

    /**
     * @param \Exception $e
     */
    public function __construct(\Exception $e) {
        $this->code = $e->getCode();
        $this->message = $e->getMessage();
        $this->file = $e->getFile();
        $this->line = $e->getLine();
        $this->trace = $e->getTrace();
        $this->originalException = $e;
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
     * @return \Exception|null
     */
    public function getOriginalException()
    {
        return $this->originalException;
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