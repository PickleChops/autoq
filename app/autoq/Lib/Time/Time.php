<?php

/**
 * User: bstratton
 * Date: 24/07/2016
 * Time: 19:11
 */

namespace Autoq\Lib\Time;

/**
 * Class Time Useful time components
 * @package Autoq\Lib\Time
 */
class Time
{

    protected $timestamp;

    protected $currentDate;
    protected $currentTime;
    protected $currentMinute;
    protected $currentHour;
    protected $currentWeekDay;
    protected $currentMonthDay;
    protected $currentMonth;
    protected $currentMonthLastDay;
    protected $currentYear;


    /**
     * Time constructor.
     * @param null $time
     */
    public function __construct($time = null) {
        
        $this->timestamp = $time === null ? time() : $time;

        $this->currentDate = date('Y-M-d', $this->timestamp);
        $this->currentTime = date('H:i', $this->timestamp);
        $this->currentMinute = date('i', $this->timestamp);
        $this->currentHour = date('H', $this->timestamp);
        $this->currentWeekDay = date('N', $this->timestamp);
        $this->currentMonthDay = date('j', $this->timestamp);
        $this->currentMonth = date('n', $this->timestamp);
        $this->currentMonthLastDay = date('t', $this->timestamp);
        $this->currentYear = date('Y', $this->timestamp);

    }

    /**
     * @return int|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    
    /**
     * @return bool|string
     */
    public function getCurrentMinute()
    {
        return $this->currentMinute;
    }

    /**
     * @return bool|string
     */
    public function getCurrentHour()
    {
        return $this->currentHour;
    }

    /**
     * @return bool|string
     */
    public function getCurrentWeekDay()
    {
        return $this->currentWeekDay;
    }


    /**
     * @return bool|string
     */
    public function getCurrentMonthDay()
    {
        return $this->currentMonthDay;
    }

    /**
     * @return bool|string
     */
    public function getCurrentMonth()
    {
        return $this->currentMonth;
    }


    /**
     * @return bool|string
     */
    public function getCurrentMonthLastDay()
    {
        return $this->currentMonthLastDay;
    }


    /**
     * @return bool|string
     */
    public function getCurrentYear()
    {
        return $this->currentYear;
    }


    /**
     * @return bool|string
     */
    public function getCurrentDate()
    {
        return $this->currentDate;
    }

    /**
     * @return bool|string
     */
    public function getCurrentTime()
    {
        return $this->currentTime;
    }


}