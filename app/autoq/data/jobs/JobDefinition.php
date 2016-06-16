<?php

namespace Autoq\Data\Jobs;

use Autoq\Data\Arrayable;
use Autoq\Lib\ScheduleParser\Schedule;
use Autoq\Lib\ScheduleParser\ScheduleParser;

/**
 * Class JobDefinition
 */
class JobDefinition implements Arrayable
{
    private $id;
    private $name;
    private $scheduleOriginal;
    private $schedule;
    private $connection;
    private $query;

    private $created;
    private $updated;

    private $outputs = [];


    const OUTPUT_EMAIL = "email";
    const OUTPUT_S3 = "s3";


    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setConnection($data['connection']);
        $this->setQuery($data['query']);
        $this->setCreated($data['created']);
        $this->setUpdated($data['updated']);
  
        $this->setScheduleOriginal($data['schedule']);

        $scheduleParser = new ScheduleParser($data['schedule']);
        if (($schedule = $scheduleParser->parse()) instanceof Schedule) {
            $this->setSchedule($schedule);
        } else {
            throw new \Exception("Unable to parse schedule in " . __CLASS__);
        }
        
        if (count($data['outputs'])) {

            foreach ($data['outputs'] as $outputData) {

                if ($outputData['type'] == self::OUTPUT_EMAIL) {
                    $output = new OutputEmail($outputData);
                } elseif ($outputData['type'] == self::OUTPUT_S3) {
                    $output = new OutputS3($outputData);
                } else {
                    throw new \Exception("Unknown output type ({$outputData['type']}) in job definition");
                }

                $this->addOutput($output);

            }
        }
    }

    /**
     * Convert a job definition object back to a plain array
     * @return array
     * @throws \Exception
     */
    public function toArray()
    {
        $data = [];

        $data['id'] = $this->getId();
        $data['name'] = $this->getName();
        $data['query'] = $this->getQuery();
        $data['created'] = $this->getCreated();
        $data['updated'] = $this->getUpdated();
        $data['schedule'] = $this->getScheduleOriginal();
        $data['connection'] = $this->getConnection();

        $data['outputs'] = [];

        if (count($this->getOutputs())) {
            foreach ($this->getOutputs() as $output) {

                $outputData = $output->toArray();
                array_push($data['outputs'], $outputData);

            }
        }

        return $data;

    }

    /**
     * @return string
     * @throws \Exception
     */
    public function convertToDbJson() {
        $data = $this->toArray();

        unset($data['id']);
        unset($data['created']);
        unset($data['updated']);

        if(($json = json_encode($data)) === false) {
            throw new \Exception("Unable to convert job definiton {$this->id} to JSON");
        }

        return $json;
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
    private function setCreated($created)
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
    private function setUpdated($updated)
    {
        $this->updated = $updated;
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
    private function setId($id)
    {
        $this->id = $id;
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
     * @return Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param Schedule $schedule
     */
    public function setSchedule(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }


    /**
     * @return mixed
     */
    public function getScheduleOriginal()
    {
        return $this->scheduleOriginal;
    }

    /**
     * @param $scheduleOriginal
     */
    public function setScheduleOriginal($scheduleOriginal)
    {
        $this->scheduleOriginal = $scheduleOriginal;
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