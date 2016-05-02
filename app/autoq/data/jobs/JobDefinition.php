<?php 

namespace Api\data\jobs;

/**
 * Class JobDefinition
 */
class JobDefinition
{
    private $id;
    private $name;
    private $schedule;
    private $connection;
    private $query;

    private $outputs;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param mixed $schedule
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return Output[]
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     * @param Output $output
     */
    public function addOutput(Output $output)
    {
        array_push($this->outputs, $output);
    }

    /**
     * Return count of outputs
     */
    public function countOutputs()
    {
       return count($this->outputs);
    }

}