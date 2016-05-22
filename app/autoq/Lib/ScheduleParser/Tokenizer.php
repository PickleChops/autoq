<?php

namespace Autoq\Lib\ScheduleParser;


class Tokenizer implements \Iterator
{
    private $input;
    
    private $tokens;
    
    private $position = 0;
    
    private $count = 0;

    /**
     * Tokenizer constructor.
     * @param $input
     * @param string $separators
     */
    public function __construct($input, $separators = " \n\t") {
        $this->input = $input;
        $this->tokens = $this->tokenize($input, $separators);
        $this->count = count($this->tokens);
    }


    /**
     * Return string as tokens based on whitespace
     * @param $input
     * @param $separators
     * @return array
     */
    private function tokenize($input, $separators) {
        
        $tokens = [];

        $token = strtok($input, $separators);

        while ($token !== false) {
            $tokens[] = $token;
            $token = strtok($separators);
        }
        
        return $tokens;
    }

    /**
     * @return mixed
     */
    public function rewind() {
        $this->position = 0;
        return $this->current();
    }

    /**
     * @return mixed
     */
    public function current() {
        return $this->position < $this->count ? $this->tokens[$this->position] : false;
    }

    /**
     * @return mixed
     */
    public function peek() {
        return $this->position < $this->count - 1 ? $this->tokens[$this->position + 1] : false;
    }

    /**
     * @return mixed
     */
    public function next() {
        return $this->position < $this->count ? $this->tokens[$this->position++] : false;
    }

    /**
     * @return mixed
     */
    public function prev() {
        return $this->position ? $this->tokens[--$this->position] : false;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return mixed
     */
    public function key()
    {
      return($this->position);
    }

    /**
     * @return mixed
     */
    public function valid()
    {
       return isset($this->tokens[$this->position]);
    }


}