<?php
namespace Autoq\Data\Jobs;
/**
 * Class OutputEntity
 */
class OutputEmail extends Output
{

    private $email;

    public function __construct($data)
    {
        $this->setType(JobDefinition::OUTPUT_EMAIL);
        $this->setEmail($data['address']);
        $this->setFormat($data['format']);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Convert to an array
     * @return array
     */
    public function toArray() {

        return [
            'type' => $this->getType(),
            'address' => $this->getEmail(),
            'format' => $this->getFormat()
        ];
    }
    
}