<?php

namespace Autoq\Lib\ScheduleParser;

class ScheduleParser
{
    private $lexer;
    private $schedule;

    /**
     * ScheduleParser constructor.
     * @param $input
     */
    public function __construct($input)
    {
        $this->lexer = new ScheduleLexer(new Tokenizer($input));
        $this->schedule = new Schedule();
    }

    /**
     * Return Schedule object based on input to this object
     * @return Schedule
     */
    public function parse()
    {
        while (($token = $this->lexer->next())) {

            $tokenType = $token['type'];



            //Add frequency if found in input
            if ($tokenType == ScheduleLexer::TYPE_KEYWORD_FREQUENCY) {
                $this->lookForFrequency();
            }

            //If a date is specified add to schedule
            if ($tokenType == ScheduleLexer::TYPE_DATE) {
                $this->schedule->setDate($token['normalised']);
            }

            //If a time is specified add to schedule
            if ($tokenType == ScheduleLexer::TYPE_TIME) {
                $this->schedule->setTime($token['normalised']);
            }

            //If a day is specified add to schedule
            if ($tokenType == ScheduleLexer::TYPE_KEYWORD_DAY) {
                $this->schedule->setDay($token['normalised']);
            }

            //if an integer is found check for minutes
            if ($tokenType == ScheduleLexer::TYPE_POSITIVE_INT) {
                $this->lookForMinutes($token['normalised']);
            }

            //if ASAP is found check for minutes
            if ($tokenType == ScheduleLexer::TYPE_KEYWORD_ASAP) {
                $this->schedule->setFrequency(Schedule::ASAP);
                $this->schedule->setAsap(true);
            }

            //if ASAP is found check for minutes
            if ($tokenType == ScheduleLexer::TYPE_KEYWORD_NONE) {
                $this->schedule->setFrequency(Schedule::NONE);
            }

        }

        $this->resolveConflicts();

        return $this->schedule->isValid() ? $this->schedule : false;

    }


    /**
     * Remove any inconsistences in the schedule
     */
    private function resolveConflicts()
    {
        //If an explicit date is set ignore other scheduling
        if (($date = $this->schedule->getDate()) !== false) {
            $this->schedule->reset()->setDate($date);
            $this->schedule->setFrequency(Schedule::FIXED_TIME);
        }

        //If just a time is set then assume fixed time
        if (($time = $this->schedule->getTime()) !== false && $this->schedule->getFrequency() === false) {
            $this->schedule->reset()->setTime($time);
            $this->schedule->setFrequency(Schedule::FIXED_TIME);
        }

        //If ASAP found ignore others
        if (($asap = $this->schedule->getAsap()) !== false) {
            $this->schedule->reset()->setAsap($asap);
            $this->schedule->setFrequency(Schedule::ASAP);
        }

        //If weeekly but no time given assume midnight
        if($this->schedule->getFrequency() == Schedule::WEEKLY && $this->schedule->getTime() === false) {
            $this->schedule->setTime('00:00');
        }

        //If NONE found ignore others
        if ($this->schedule->getFrequency() === Schedule::NONE) {
            $this->schedule->reset()->setFrequency(Schedule::NONE);
        }

    }

    /**
     * Look for a frequency
     */
    private function lookForFrequency()
    {

        if (($nextToken = $this->lexer->current()) !== false) {

            $nextTokenType = $nextToken['type'];

            switch ($nextTokenType) {

                case ScheduleLexer::TYPE_KEYWORD_DURATION_HOUR:
                    $this->schedule->setFrequency(Schedule::HOURLY);
                    break;

                case ScheduleLexer::TYPE_KEYWORD_DURATION_DAY:
                    $this->schedule->setFrequency(Schedule::DAILY);
                    break;

                case ScheduleLexer::TYPE_KEYWORD_DURATION_WEEK:
                    $this->schedule->setFrequency(Schedule::WEEKLY);
                    break;

                case ScheduleLexer::TYPE_KEYWORD_DAY:
                    $this->schedule->setFrequency(Schedule::WEEKLY);
                    $this->schedule->setDay($nextToken['normalised']);
                    break;

            }
        }
    }

    /**
     * Look for a frequency
     * @param $minutes
     */
    private function lookForMinutes($minutes)
    {

        if (($nextToken = $this->lexer->current()) !== false) {

            $nextTokenType = $nextToken['type'];

            if ($nextTokenType == ScheduleLexer::TYPE_FREE_TEXT) {
                //@todo This probably ought to be in the lexer
                if (in_array(strtolower($nextToken['normalised']), ['mins', 'minute', 'minutes'])) {
                    $this->schedule->setMinute($minutes);
                }
            }

        }
    }


}