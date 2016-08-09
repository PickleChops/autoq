<?php
namespace Autoq\Data\S3Credentials;

use Autoq\Data\Arrayable;

/**
 * Class S3Credential Internal representation of a S3 Credential set
 * @package Autoq\Data\Jobs
 */
class S3Credential implements Arrayable
{
    private $id;
    private $alias;
    private $key;
    private $secret;

    private $created;
    private $updated;


 
    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setAlias($data['alias']);
        $this->setKey($data['key']);
        $this->setSecret($data['secret']);
        
        $this->setCreated($data['created']);
        $this->setUpdated($data['updated']);

    }

    /**
     * Convert a S3Credential object back to a plain array
     * @return array
     * @throws \Exception
     */
    public function toArray()
    {
        $data = [];

        $data['id'] = $this->getId();
        $data['alias'] = $this->getAlias();
        $data['key'] = $this->getKey();
        $data['secret'] = $this->getSecret();
      
        return $data;
    }

    /**
     * As array for config
     */
    public function asConfigArray(){

        $data = $this->toArray();
        unset($data['id']);
        unset($data['alias']);
        
        return $data;
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param mixed $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
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
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

   
}