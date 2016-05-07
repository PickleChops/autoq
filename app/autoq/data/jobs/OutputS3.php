<?php
namespace Autoq\Data\Jobs;
/**
 * Class OutputS3
 */
class OutputS3 extends Output
{
    private $bucket;
    
    
    public function __construct($data)
    {
        $this->setType(JobDefinition::OUTPUT_S3);
        $this->setBucket($data['bucket']);
        $this->setFormat($data['format']);
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
     * Convert to an array
     * @return array
     */
    public function toArray() {

        return [
                'type' => $this->getType(),
                'bucket' => $this->getBucket(),
                'format' => $this->getFormat()
        ];
    }
    
    
}