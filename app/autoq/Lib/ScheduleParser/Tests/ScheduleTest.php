<?php

namespace Tests;


use Autoq\Lib\ScheduleParser\Schedule;
use Autoq\Lib\ScheduleParser\ScheduleParser;
use Autoq\Lib\Time\Time;


require_once __DIR__ . '/../Schedule.php';
require_once __DIR__ . '/../../Time/Time.php';


class ScheduleTest extends \PHPUnit_Framework_TestCase
{
    public function testNextASAPEvent()
    {
        $schedule = new Schedule();

        $schedule->setFrequency(Schedule::ASAP);

        $now = time();
        $time = new Time($now);

        $startTime = $schedule->getNextEventTs($time);

        // Assertions
        $this->assertEquals($now, $startTime);

    }


    public function testNextFixedTimeEvent1()
    {
        $schedule = new Schedule();

        $scheduleDate = '2016-12-22';
        $scheduleTime = '16:00';

        $schedule
            ->setFrequency(Schedule::FIXED_TIME)
            ->setDate($scheduleDate)
            ->setTime($scheduleTime);

        $now = time();
        $time = new Time($now);


        $fixedTimeTS = strtotime("$scheduleDate $scheduleTime");

        $startTime = $schedule->getNextEventTs($time);

        // Assertions
        $this->assertEquals($fixedTimeTS, $startTime);

    }


    public function testNextFixedTimeEvent2()
    {
        $schedule = new Schedule();

        $pretendDate = "2016-12-22";

        $pretendActualTime = strtotime("$pretendDate 10:00");

        $scheduleTime = '16:00';

        $schedule
            ->setFrequency(Schedule::FIXED_TIME)
            ->setTime($scheduleTime);

        $time = new Time($pretendActualTime);

        $fixedTimeTS = strtotime("$pretendDate $scheduleTime");

        $startTime = $schedule->getNextEventTs($time);

        // Assertions
        $this->assertEquals($fixedTimeTS, $startTime,"No date provided, should use 'today'");

    }

    /**
     * @dataProvider testNextHourlyEventProvider
     * @param $pretendTime
     * @param $minutesPastHour
     * @param $expectedTime
     */
    public function testNextHourlyEvent($pretendTime, $minutesPastHour, $expectedTime)
    {
        $schedule = new Schedule();

        $expectedTS = strtotime($expectedTime);

        $pretendActualTime = strtotime($pretendTime);

        $schedule
            ->setFrequency(Schedule::HOURLY)
            ->setMinute($minutesPastHour);

        $time = new Time($pretendActualTime);

        $startTime = $schedule->getNextEventTs($time);

        // Assertions
        $this->assertEquals($expectedTS, $startTime);

    }

    /**
     * Provider for testNextHourlyEvent
     * @return array
     */
    public function testNextHourlyEventProvider() {

        return [

          ['2016-12-22 10:00', 5, '2016-12-22 10:05'],
          ['2016-12-22 10:10', 5, '2016-12-22 11:05'],
          ['2016-12-22 10:10', 0, '2016-12-22 11:00'],
          ['2016-12-22 10:00', 0, '2016-12-22 10:00'],
          ['2016-12-22 10:00', 59, '2016-12-22 10:59']

        ];

    }


    /**
     * @dataProvider testNextDailyEventProvider
     * @param $pretendTime
     * @param $timeInDay
     * @param $expectedTime
     */
    public function testNextDailyEvent($pretendTime, $timeInDay, $expectedTime)
    {
        $schedule = new Schedule();

        $expectedTS = strtotime($expectedTime);

        $pretendActualTime = strtotime($pretendTime);

        $schedule
            ->setFrequency(Schedule::DAILY)
            ->setTime($timeInDay);

        $time = new Time($pretendActualTime);

        $startTime = $schedule->getNextEventTs($time);

        // Assertions
        $this->assertEquals($expectedTS, $startTime);

    }

    /**
     * Provider for testNextDailyEvent
     * @return array
     */
    public function testNextDailyEventProvider() {

        return [

            ['2016-12-22 15:00', '16:00', '2016-12-22 16:00'],
            ['2016-12-22 15:00', '14:00', '2016-12-23 14:00'],
            ['2016-12-31 23:00', '00:00', '2017-01-01 00:00'],

        ];

    }

    /**
     * @dataProvider testNextWeeklyEventProvider
     * @param $pretendTime
     * @param $weekday
     * @param $timeInDay
     * @param $expectedTime
     */
    public function testNextWeeklyEvent($pretendTime, $weekday, $timeInDay, $expectedTime)
    {

        $expectedTS = strtotime($expectedTime);

        $pretendActualTime = strtotime($pretendTime);

        $schedule = new Schedule();

        $schedule
            ->setFrequency(Schedule::WEEKLY)
            ->setDay($weekday)
            ->setTime($timeInDay);

        $time = new Time($pretendActualTime);

        $startTime = $schedule->getNextEventTs($time);

        // Assertions
        $this->assertEquals($expectedTS, $startTime);

    }


    /**
     * Provider for testNextWeeklyEvent
     * @return array
     */
    public function testNextWeeklyEventProvider() {

        return [

            ['2016-06-01 15:00', 'Tuesday', '15:00', '2016-06-07 15:00'],
            ['2016-06-01 15:00', 'Friday', '09:00', '2016-06-03 09:00'],
            ['2016-12-25 23:59', 'Sunday', '10:00', '2017-01-01 10:00'],

        ];

    }

}
