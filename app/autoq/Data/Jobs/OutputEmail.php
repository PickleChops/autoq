<?php
namespace Autoq\Data\Jobs;
/**
 * Class OutputEntity
 */
class OutputEmail extends Output
{
    const STYLE_HTML = 'html';
    const STYLE_ATTACHMENT = 'attachment';

    private $email;
    private $style;

    public function __construct($data)
    {
        $this->setType(JobDefinition::OUTPUT_EMAIL);
        $this->setEmail($data['address']);
        $this->setStyle(array_get('style', $data, self::STYLE_ATTACHMENT));
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
     * @return mixed
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param mixed $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
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
            'style' => $this->getStyle()
        ];
    }

}