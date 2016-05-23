<?php

namespace Autoq\Lib\ScheduleParser;


class Schedule
{

    const NO_FREQUENCY = 0;
    const HOURLY = 1;
    const DAILY = 2;
    const WEEKLY = 3;

    private $flexible;

    private $frequency = self::NO_FREQUENCY;
    private $date = false;
    private $time = false;
    private $minute = false;
    private $hour = false;
    private $day = false;


    /**
     * Schedule constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * @param mixed $frequency
     * @return Schedule
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }


    /**
     * @param mixed $minute
     * @return Schedule
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;
        return $this;
    }

    /**
     * @param mixed $hour
     * @return Schedule
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
        return $this;
    }

    /**
     * @param mixed $day
     * @return Schedule
     */
    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @param boolean $flexible
     * @return Schedule
     */
    public function setFlexible($flexible)
    {
        $this->flexible = $flexible;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @return mixed
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * @return mixed
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @return boolean
     */
    public function isFlexible()
    {
        return $this->flexible;
    }

    /**
     * @return boolean
     */
    public function isDate()
    {
        return $this->date;
    }

    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTime()
    {
        return $this->time;
    }

    /**
     * @param boolean $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return bool
     */
    public function getTime() {
        return $this->time;
    }

    /**
     * @return mixed
     */
    public function getFlexible()
    {
        return $this->flexible;
    }
    
    /**
     * Reset the schedule
     */
    public function reset()
    {
        $this->frequency = self::NO_FREQUENCY;
        $this->date = false;
        $this->minute = false;
        $this->hour = false;
        $this->day = false;
        
        return $this;
    }

}