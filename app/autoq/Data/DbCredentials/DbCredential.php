<?php
namespace Autoq\Data\DbCredentials;

use Autoq\Data\Arrayable;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Adapter\Pdo\Postgresql;


/**
 * Class DbCredential Internal representation of a Db Credential set
 * @package Autoq\Data\Jobs
 */
class DbCredential implements Arrayable
{
    private $id;
    private $alias;
    private $host;
    private $port;
    private $adapter;
    private $database;
    private $username;
    private $password;

    private $created;
    private $updated;


    private $driverMap = [

        'POSTGRES' =>  Postgresql::class,
        'MYSQL' => Mysql::class

    ];

    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setAlias($data['alias']);
        $this->setHost($data['host']);
        $this->setPort($data['port']);
        $this->setAdapter($data['adapter']);
        $this->setDatabase($data['database']);
        $this->setUsername($data['username']);
        $this->setPassword($data['password']);

        $this->setCreated($data['created']);
        $this->setUpdated($data['updated']);

    }

    /**
     * Convert a DbCredential object back to a plain array
     * @return array
     * @throws \Exception
     */
    public function toArray()
    {
        $data = [];

        $data['id'] = $this->getId();
        $data['alias'] = $this->getAlias();
        $data['host'] = $this->getHost();
        $data['port'] = $this->getPort();
        $data['adapter'] = $this->getAdapter();
        $data['database'] = $this->getDatabase();
        $data['username'] = $this->getUsername();
        $data['password'] = $this->getPassword();
      
        return $data;

    }

    /**
     * As array for config
     */
    public function asConfigArray(){
       
        $data = $this->toArray();
        unset($data['id']);
        unset($data['alias']);

        if(($className = array_get($data['adapter'],$this->driverMap, null)) === null) {
            throw new \Exception("Unknown db adapter: " .$data['adapter']);
        }
        
        $data['adapter'] = $className;
        
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
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param mixed $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param mixed $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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