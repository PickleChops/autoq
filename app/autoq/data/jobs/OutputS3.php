<?php
namespace Api\data\jobs;
/**
 * Class OutputS3
 */
class OutputS3 extends Output
{
    private $bucket;

    /**
     * @return mixed
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param mixed $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }
    
    
}