<?php

namespace Autoq\Services\JobProcessor\ItemProcessors;

use Autoq\Lib\ScheduleParser\Schedule;
use Autoq\Lib\ScheduleParser\ScheduleParser;
use Autoq\Services\JobProcessor\Utils\ItemFilters;
use Autoq\Services\JobProcessor\Utils\ItemValidations;
use Autoq\Services\JobProcessor\JobProcessorErrors;

class JobScheduleProcessor extends JobItemProcessor
{
    protected $fieldName = 'schedule';

   /**
     * @param $data
     * @return mixed
     */
    protected function sanitize($data)
    {
        return ItemFilters::trim($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function validate($data)
    {
        if (!ItemValidations::exists($data)) {
            $this->addMessageByCode(JobProcessorErrors::MSG_NO_SCHEDULE);
        }

        if (!ItemValidations::maxLength($data, 255)) {
            $this->addMessageByCode(JobProcessorErrors::MSG_FIELD_DATA_TOO_LONG);
        }

        //Attempt to parse the provided schedule

        $scheduleParser = new ScheduleParser($data);

        if (!($schedule = $scheduleParser->parse()) instanceof Schedule) {
             $this->addMessageByCode(JobProcessorErrors::MSG_UNABLE_TO_PARSE_SCHEDULE);
        }
    }
}