<?php

namespace Autoq\Data\Jobs;
use Autoq\Data\Arrayable;

/**
 * Class Output
 */
abstract class Output implements Arrayable
{
    private $type;
    private $format;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return []
     */
    abstract public function toArray();   
    
}