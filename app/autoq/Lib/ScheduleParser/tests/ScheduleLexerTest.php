<?php

namespace Tests;

use Autoq\Lib\ScheduleParser\ScheduleLexer;
use Autoq\Lib\ScheduleParser\Tokenizer;

require_once __DIR__ . '/../ScheduleLexer.php';
require_once __DIR__ . '/../Tokenizer.php';

class ScheduleLexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider timeProvider
     * @param $timeString
     */
    public function testIsTime($timeString)
    {

        $lexer = new ScheduleLexer(new Tokenizer($timeString));

        $tokens = $lexer->getProcessedTokens();

        $this->assertEquals(1, count($tokens));

        $token = $tokens[0];

        $this->assertEquals(ScheduleLexer::TYPE_TIME, $token['type']);
        $this->assertEquals($timeString, $token['token']);
    }

    /**
     * Return times to test
     * @return array
     */
    public function timeProvider()
    {
        return [
            ['12:23pm'],
            ['02:23'],
            ['12:23Am'],
            ['1pm'],
            ['20:20']
        ];
    }

    /**
     * @dataProvider invalidTimeProvider
     * @param $invalidTimeString
     */
    public function testInvalidTime($invalidTimeString)
    {

        $lexer = new ScheduleLexer(new Tokenizer($invalidTimeString));

        $tokens = $lexer->getProcessedTokens();

        $this->assertEquals(1, count($tokens));

        $token = $tokens[0];

        $this->assertFalse(ScheduleLexer::TYPE_TIME == $token['type']);
    }

    /**
     * Return invalid times to test
     * @return array
     */
    public function invalidTimeProvider()
    {
        return [
            ['54:23pm'],
            ['02::23'],
            ['02A56'],
            ['20pm'],
        ];
    }

    /**
     * Check valid date year first
     */
    public function testIsDateYearFirst()
    {

        $testTest = '2016/1/12';

        $lexer = new ScheduleLexer(new Tokenizer($testTest));

        $tokens = $lexer->getProcessedTokens();

        $this->assertEquals(1, count($tokens));

        $token = $tokens[0];

        $this->assertEquals(ScheduleLexer::TYPE_DATE, $token['type']);
        $this->assertEquals($testTest, $token['normalised']);
    }

    /**
     * Check valid date year last
     */
    public function testIsDateYearLast()
    {

        $lexer = new ScheduleLexer(new Tokenizer('28/2/17'));

        $tokens = $lexer->getProcessedTokens();

        $this->assertEquals(1, count($tokens));

        $token = $tokens[0];

        $this->assertEquals(ScheduleLexer::TYPE_DATE, $token['type']);
        $this->assertEquals('2017/2/28', $token['normalised']);
    }

    /**
     * @dataProvider invalidDateProvider
     * @param $invalidDate
     */
    public function testInvalidDates($invalidDate) {
        $lexer = new ScheduleLexer(new Tokenizer($invalidDate));

        $tokens = $lexer->getProcessedTokens();

        $this->assertEquals(1, count($tokens));

        $token = $tokens[0];

        $this->assertFalse(ScheduleLexer::TYPE_DATE === $token['type']);
    }


    /**
     * Return invalid dates to test
     * @return array
     */
    public function invalidDateProvider()
    {
        return [
           ['1/1'],
           ['45/2/16'],
           ['1-55-16']
        ];
    }

    /**
     * Simple positive int tests
     */
    public function testPositiveInts() {
        $lexer = new ScheduleLexer(new Tokenizer('10 -10 AB10'));

        $tokens = $lexer->getProcessedTokens();

        $this->assertEquals(3, count($tokens));

        $this->assertEquals(ScheduleLexer::TYPE_POSITIVE_INT, $tokens[0]['type']);
        $this->assertEquals(10, $tokens[0]['normalised']);

        $this->assertFalse(ScheduleLexer::TYPE_POSITIVE_INT === $tokens[1]['type']);
        $this->assertFalse(ScheduleLexer::TYPE_POSITIVE_INT === $tokens[2]['type']);

    }

    /**
     * Simple keyword tests
     */
    public function testKeywords() {
        $lexer = new ScheduleLexer(new Tokenizer('Something Every tuesday in MaY'));

        $tokens = $lexer->getProcessedTokens();

        $this->assertEquals(5, count($tokens));

        $this->assertEquals(ScheduleLexer::TYPE_KEYWORD_FREQUENCY, $tokens[1]['type']);
        $this->assertEquals('Every', $tokens[1]['normalised']);

        $this->assertEquals(ScheduleLexer::TYPE_KEYWORD_DAY, $tokens[2]['type']);
        $this->assertEquals('Tuesday', $tokens[2]['normalised']);

        $this->assertEquals(ScheduleLexer::TYPE_KEYWORD_MONTH, $tokens[4]['type']);
        $this->assertEquals('May', $tokens[4]['normalised']);

    }

}
