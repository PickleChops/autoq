<?php

namespace Tests;


use Autoq\Lib\ScheduleParser\Schedule;
use Autoq\Lib\ScheduleParser\ScheduleParser;


require_once __DIR__ . '/../ScheduleParser.php';
require_once __DIR__ . '/../Schedule.php';

class ScheduleParserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetScheduleObject()
    {
       $schedule = (new ScheduleParser())->parseAsSchedule('every day at 4pm');

        // Assert
        $this->assertEquals(true, $schedule instanceof Schedule);
    }

    /**
     *
     */
    public function testDateTimeSchedule() {
        $schedule = (new ScheduleParser())->parseAsSchedule('25/6/2016 12:45');

    }

}
