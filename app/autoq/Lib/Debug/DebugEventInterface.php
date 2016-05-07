<?php

namespace Autoq\Lib\Debug;


interface DebugEventInterface
{
    /**
     * @return number|null
     */
    public function getCode();


    /**
     * @return string|null
     */
    public function getMessage();


    /**
     * @return string|null
     */
    public function getFile();


    /**
     * @return number|null
     */
    public function getLine();


    /**
     * @return array
     */
    public function getTrace();

    /**
     * @return array
     */
    public function getMetadata();


}