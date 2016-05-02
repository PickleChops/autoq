<?php

namespace CLI;

use Phalcon\Di;

class Runner
{

    protected $di;

    public function __construct(Di $di, Array $argv)
    {
        $this->di = $di;
        
        $this->main();

    }

    /**
     * Off we go
     */
    public function main() {
        
    
        //Get all the jobs
        
        //Get last entry for this job from queue
        
        //
    
    
    
    }

}