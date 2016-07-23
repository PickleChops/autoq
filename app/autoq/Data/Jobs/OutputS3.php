<?php
namespace Autoq\Data\Jobs;
/**
 * Class OutputS3
 */
class OutputS3 extends Output
{
    private $bucket;
    private $key;

    public function __construct($data)
    {
        $this->setType(JobDefinition::OUTPUT_S3);
        $this->setBucket($data['bucket']);
        $this->setKey(array_get('key', $data, ""));
    }

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

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }


    /**
     * Convert to an array
     * @return array
     */
    public function toArray() {

        return [
                'type' => $this->getType(),
                'bucket' => $this->getBucket(),
                'key' => $this->getKey(),
        ];
    }
    
    
}