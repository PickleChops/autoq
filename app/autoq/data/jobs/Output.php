<?php

namespace Autoq\Data\Jobs;
use Autoq\Data\Arrayable;

/**
 * Class Output
 */
abstract class Output implements Arrayable
{
    private $type;
 
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
     * @return []
     */
    abstract public function toArray();   
    
}
