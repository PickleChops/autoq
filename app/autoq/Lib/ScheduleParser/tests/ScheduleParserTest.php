<?php

namespace Tests;


use Autoq\Lib\ScheduleParser\Schedule;
use Autoq\Lib\ScheduleParser\ScheduleParser;


require_once __DIR__ . '/../ScheduleParser.php';
require_once __DIR__ . '/../Schedule.php';
require_once __DIR__ . '/../ScheduleLexer.php';
require_once __DIR__ . '/../Tokenizer.php';

class ScheduleParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSchedule1()
    {
        $schedule = (new ScheduleParser('every day at 4pm'))->parse();

        // Assertions
        $this->assertEquals(true, $schedule instanceof Schedule);
        $this->assertEquals(Schedule::DAILY,$schedule->getFrequency());
        $this->assertEquals('16:00',$schedule->getTime());
        
    }

    public function testSchedule2()
    {
        $schedule = (new ScheduleParser('every Wednesday at 5:05'))->parse();

        // Assertions
        $this->assertEquals(true, $schedule instanceof Schedule);
        $this->assertEquals(Schedule::WEEKLY,$schedule->getFrequency());
        $this->assertEquals('Wednesday',$schedule->getDay());
        $this->assertEquals('05:05',$schedule->getTime());

    }

    public function testSchedule3()
    {
        $schedule = (new ScheduleParser('3 minutes past every hour'))->parse();

        // Assertions
        $this->assertEquals(true, $schedule instanceof Schedule);
        $this->assertEquals(Schedule::HOURLY,$schedule->getFrequency());
        $this->assertEquals('3',$schedule->getMinute());

    }

    public function testSchedule4()
    {
        $schedule = (new ScheduleParser('every week on a Monday at 1pm'))->parse();

        // Assertions
        $this->assertEquals(true, $schedule instanceof Schedule);
        $this->assertEquals(Schedule::WEEKLY,$schedule->getFrequency());
        $this->assertEquals('Monday',$schedule->getDay());
        $this->assertEquals('13:00',$schedule->getTime());

    }
    
}
