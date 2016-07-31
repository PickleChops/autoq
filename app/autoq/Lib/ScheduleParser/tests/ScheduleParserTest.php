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

    public function testSchedule5()
    {
        $schedule = (new ScheduleParser('every week on a Friday'))->parse();

        // Assertions
        $this->assertEquals(true, $schedule instanceof Schedule);
        $this->assertEquals(Schedule::WEEKLY,$schedule->getFrequency());
        $this->assertEquals('Friday',$schedule->getDay());
        $this->assertEquals('00:00',$schedule->getTime());

    }

    public function testNotASchedule()
    {
        $schedule = (new ScheduleParser('Not a schedule'))->parse();
        
        // Assertions
        $this->assertTrue($schedule === false);


    }

    public function testASAP()
    {
        $schedule = (new ScheduleParser('ASAP'))->parse();

        // Assertions
        $this->assertEquals(true, $schedule instanceof Schedule);
        $this->assertEquals(Schedule::ASAP,$schedule->getFrequency());
        $this->assertEquals(true,$schedule->getAsap());

    }
    
}
