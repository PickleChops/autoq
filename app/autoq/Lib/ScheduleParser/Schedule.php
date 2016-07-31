<?php

namespace Autoq\Lib\ScheduleParser;

use Autoq\Lib\Time\Time;

/**
 * Class Schedule
 * @package Autoq\Lib\ScheduleParser
 */
class Schedule
{

    const NONE = 0;
    const ASAP = 1;
    const FIXED_TIME = 2;
    const HOURLY = 3;
    const DAILY = 4;
    const WEEKLY = 5;

    static private $readableFrequency = [
        self::NONE => 'NONE',
        self::ASAP => 'ASAP',
        self::FIXED_TIME => 'Fixed time',
        self::HOURLY => 'Hourly',
        self::DAILY => 'Daily',
        self::WEEKLY => 'Weekly'
    ];

    private $flexible;

    private $frequency = false;
    private $date = false;
    private $time = false;
    private $minute = false;
    private $hour = false;
    private $day = false;
    private $asap = false;


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
    public function getReadableFrequency()
    {
        return array_get($this->frequency, self::$readableFrequency, 'Unknown');
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
    public function getTime()
    {
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
        $this->frequency = false;
        $this->date = false;
        $this->minute = false;
        $this->hour = false;
        $this->day = false;
        $this->asap = false;

        return $this;
    }


    /**
     * Is this schedule cyclical
     */
    public function isCyclical()
    {

        return ($this->getFrequency() == self::HOURLY ||
            $this->getFrequency() == self::DAILY ||
            $this->getFrequency() == self::WEEKLY);
    }


    /**
     * @return bool
     */
    public function isValid()
    {

        $valid = false;

        if($this->frequency === false) {
            return false;
        }

        switch ($this->frequency) {

            case self::NONE:
                $valid = true;
                break;

            case self::ASAP:
                $valid = true;
                break;

            case self::FIXED_TIME:

                if ($this->date !== false || $this->time !== false) {
                    $valid = true;
                }
                break;

            case self::HOURLY:

                $valid = true;
                break;

            case self::DAILY:

                if ($this->time !== false) {
                    $valid = true;
                }
                break;


            case self::WEEKLY:
                // @todo
                $valid = true;
                break;

        }

        return $valid;
    }

    /**
     * @return boolean
     */
    public function getAsap()
    {
        return $this->asap;
    }

    /**
     * @param boolean $asap
     */
    public function setAsap($asap)
    {
        $this->asap = $asap;
    }

    /**
     * Return timestamp for next event for this schedule
     * @param Time $time
     * @return bool|int|null
     */
    public function getNextEventTs(Time $time)
    {
        $nextEventTimeStamp = false;

        switch ($this->frequency) {

            case self::NONE:
                break;

            case self::ASAP:

                $nextEventTimeStamp = $time->getTimestamp();

                break;

            case self::FIXED_TIME:

                $date = $this->getDate() !== false ? $this->getDate() : $time->getCurrentDate();
                $time = $this->getTime() !== false ? $this->getTime() : '00:00';
                $nextEventTimeStamp = strtotime("$date $time");

                break;

            case self::HOURLY:

                $actualDateTime = (new \DateTime())->setTimestamp($time->getTimestamp());

                $minutes = $this->getMinute() !== false ? intval($this->getMinute()) : 0;

                $scheduleDateTime = (new \DateTime())->setDate(
                    $time->getCurrentYear(),
                    $time->getCurrentMonth(),
                    $time->getCurrentMonthDay()
                )->setTime(
                    intval($time->getCurrentHour()),
                    $minutes
                );

                if ($scheduleDateTime < $actualDateTime) {
                    $scheduleDateTime->add(new \DateInterval("PT1H"));
                }

                $nextEventTimeStamp = $scheduleDateTime->getTimestamp();

                break;

            case self::DAILY:

                $actualDateTime = (new \DateTime())->setTimestamp($time->getTimestamp());

                $scheduleTime = $this->getTime() !== false ? $this->getTime() : '00:00';

                $timeParts = explode(':', $scheduleTime);

                $hour = $timeParts[0];
                $minutes = intval($timeParts[1]);

                $scheduleDateTime = (new \DateTime())->setDate(
                    $time->getCurrentYear(),
                    $time->getCurrentMonth(),
                    $time->getCurrentMonthDay()
                )->setTime(
                    $hour,
                    $minutes
                );

                if ($scheduleDateTime < $actualDateTime) {
                    $scheduleDateTime->add(new \DateInterval("P1D"));
                }

                $nextEventTimeStamp = $scheduleDateTime->getTimestamp();

                break;


            case self::WEEKLY:

                $actualDateTime = (new \DateTime())->setTimestamp($time->getTimestamp());

                $scheduleTsFromDay = strtotime("next {$this->getDay()}", $time->getTimestamp());

                $timeParts = explode(':', $this->getTime());

                $hour = $timeParts[0];
                $minutes = intval($timeParts[1]);

                $scheduleDateTime = (new \DateTime())->setTimestamp($scheduleTsFromDay);
                $scheduleDateTime->setTime($hour, $minutes);

                if ($scheduleDateTime < $actualDateTime) {
                    $scheduleDateTime->add(new \DateInterval("P1W"));
                }

                $nextEventTimeStamp = $scheduleDateTime->getTimestamp();

                break;

        }

        return $nextEventTimeStamp;

    }

}