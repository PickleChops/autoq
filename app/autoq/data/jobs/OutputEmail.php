<?php
namespace Autoq\Data\Jobs;
/**
 * Class OutputEntity
 */
class OutputEmail extends Output
{
    const FORMAT_HTML = 'html';
    const FORMAT_ATTACHMENT = 'attachment';

    private $email;

    public function __construct($data)
    {
        $this->setType(JobDefinition::OUTPUT_EMAIL);
        $this->setEmail($data['address']);
        $this->setFormat(array_get('format', $data, self::FORMAT_ATTACHMENT));
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
    public function toArray()
    {

        return [
            'type' => $this->getType(),
            'address' => $this->getEmail(),
            'format' => $this->getFormat()
        ];
    }

}