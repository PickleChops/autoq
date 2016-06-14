<?php

namespace Autoq\Lib\ScheduleParser;

class ScheduleLexer implements \Iterator
{
    const TYPE_TIME = 'time';
    const TYPE_DATE = 'date';
    const TYPE_POSITIVE_INT = 'int';
    const TYPE_FREE_TEXT = 'free_text';

    const TYPE_KEYWORD_FREQUENCY = 'frequency';
    const TYPE_KEYWORD_DURATION_HOUR = 'duration_hour';
    const TYPE_KEYWORD_DURATION_DAY = 'duration_day';
    const TYPE_KEYWORD_DURATION_WEEK = 'duration_week';
    const TYPE_KEYWORD_DURATION_MONTH = 'duration_month';
    const TYPE_KEYWORD_DAY = 'day';
    const TYPE_KEYWORD_MONTH = 'month';
    const TYPE_KEYWORD_ASAP = 'asap';

    private $keywords = [
        
        self::TYPE_KEYWORD_ASAP => [
            'now', 'asap'
        ],

        self::TYPE_KEYWORD_FREQUENCY => [
            'Every', 'Each'
        ],

        self::TYPE_KEYWORD_DURATION_HOUR => [
            'Hour'
        ],

        self::TYPE_KEYWORD_DURATION_DAY => [
            'Day'
        ],

        self::TYPE_KEYWORD_DURATION_WEEK => [
            'Week'
        ],

        self::TYPE_KEYWORD_DURATION_MONTH => [
            'Month'
        ],

        self::TYPE_KEYWORD_DAY => [
            ['Mon', 'Tues', 'Wed', 'Thr', 'Fri', 'Sat', 'Sun'],
            ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
        ],

        self::TYPE_KEYWORD_MONTH => [
            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        ],

    ];

    private $processedTokens = [];
    private $position = 0;
    private $count = 0;

    /**
     * ScheduleParser constructor.
     * @param Tokenizer $tokenizer
     */
    public function __construct(Tokenizer $tokenizer)
    {
        $this->count = $tokenizer->getCount();

        foreach ($tokenizer as $token) {

            if (($date = $this->isDate($token)) !== false) {
                $this->addProcessedToken($token, self::TYPE_DATE, $date);
            } elseif (($time = $this->isTime($token)) !== false) {
                $this->addProcessedToken($token, self::TYPE_TIME, $time);
            } elseif (($positiveInt = $this->isPositiveInt($token)) !== false) {
                $this->addProcessedToken($token, self::TYPE_POSITIVE_INT, $positiveInt);
            } elseif (($keyword = $this->isKeyword($token, $keywordType)) !== false) {
                $this->addProcessedToken($token, $keywordType, $keyword);
            } else {
                $this->addProcessedToken($token, self::TYPE_FREE_TEXT, $token);
            }
        }

 //       var_dump($this->processedTokens);
    }

    /**
     * Return the tokens
     * @return array
     */
    public function getProcessedTokens()
    {
        return $this->processedTokens;
    }

    /**
     * Is the token a 'keyword'
     * @param $token
     * @param $returnKeywordType
     * @return bool|string
     */
    private function isKeyword($token, &$returnKeywordType)
    {
        $properCaseToken = ucfirst(strtolower($token));
        $returnKeywordType = null;

        foreach ($this->keywords as $keywordType => $collection) {

            foreach ($collection as $wordOrArray) {

                if (is_array($wordOrArray)) {

                    if (in_array($properCaseToken, $wordOrArray)) {
                        $returnKeywordType = $keywordType;
                        break 2;
                    }

                } elseif ($properCaseToken == $wordOrArray) {
                    $returnKeywordType = $keywordType;
                    break 2;
                }

            }
        }

        return ($returnKeywordType !== null) ? $properCaseToken : false;

    }

    /**
     * Does the token look like a time?
     * @param $token
     * @return bool
     */
    private function isTime($token)
    {
        $time = false;

        /**
         * Normalise time to 24hr
         * @param $hour
         * @param $minute
         * @param null $ampm
         * @return string
         */
        $normaliseHelper = function ($hour, $minute, $ampm = null) {

            if (strtolower($ampm) == 'pm' && (int)$hour >= 1 && (int)$hour < 12) {
                $hour += 12;
            }

            if (strtolower($ampm) == 'am' && (int)$hour == 12) {
                $hour -= 12;
            }

            return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT);

        };

        if (preg_match('/^(1[0-2]|0?[1-9])(am|pm)$/i', $token, $matches) > 0) {
            $time = $normaliseHelper($matches[1], 0, $matches[2]);
        } elseif (preg_match('/^(1[0-2]|0?[1-9]):([0-5][0-9])(am|pm)$/i', $token, $matches) > 0) {
            $time = $normaliseHelper($matches[1], $matches[2], $matches[3]);
        } elseif (preg_match('/^(1[0-9]|2[0-3]|0?[0-9]):([0-5][0-9])$/i', $token, $matches) > 0) {
            $time = $normaliseHelper($matches[1], $matches[2]);
        }

        return $time;
    }

    /**
     * Does the token look like a date?
     * @param $token
     * @return bool
     */
    private function isDate($token)
    {
        $date = false;

        $year = null;
        $month = null;
        $day = null;

        if (preg_match("~^(20\\d{2})(?:/|-)(\\d{1,2})(?:/|-)(\\d{1,2})$~i", $token, $matches) > 0) {

            //e.g. 2016-12-31

            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];

        } elseif (preg_match("~^(\\d{1,2})(?:/|-)(\\d{1,2})(?:/|-)(\\d{2,4})$~i", $token, $matches) > 0) {

            //e.g. 31/12/16

            $year = (strlen($matches[3]) == 2) ? "20{$matches[3]}" : $year;
            $month = $matches[2];
            $day = $matches[1];

        }

        if (checkdate($month, $day, $year)) {
            $date = "$year/$month/$day";
        }

        return $date;
    }


    /**
     * Is token a positive integet
     * @param $token
     * @return int|bool
     */
    private function isPositiveInt($token)
    {

        $val = intval($token);
        return $val > 0 ? $val : false;

    }

    /**
     * Store processed tokens
     * @param $token
     * @param $type
     * @param $normalised
     */
    private function addProcessedToken($token, $type, $normalised)
    {
        array_push($this->processedTokens, ['type' => $type, 'token' => $token, 'normalised' => $normalised]);
    }

    /**
     * @return mixed
     */
    public function rewind()
    {
        $this->position = 0;
        return $this->current();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->position < $this->count ? $this->processedTokens[$this->position] : false;
    }

    /**
     * @return mixed
     */
    public function peek()
    {
        return $this->position < $this->count - 1 ? $this->processedTokens[$this->position + 1] : false;
    }

    /**
     * @return mixed
     */
    public function next()
    {
        return $this->position < $this->count ? $this->processedTokens[$this->position++] : false;
    }

    /**
     * @return mixed
     */
    public function prev()
    {
        return $this->position ? $this->processedTokens[--$this->position] : false;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return ($this->position);
    }

    /**
     * @return mixed
     */
    public function valid()
    {
        return isset($this->processedTokens[$this->position]);
    }

}