<?php

namespace Autoq\Services\JobProcessor\ItemProcessors;

use Autoq\Services\JobProcessor\Utils\ItemFilters;
use Autoq\Services\JobProcessor\Utils\ItemValidations;
use Autoq\Services\JobProcessor\JobProcessorErrors;

class JobNameProcessor extends JobItemProcessor
{
    protected $fieldName = 'name';

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
            $this->addMessageByCode(JobProcessorErrors::MSG_NO_JOB_NAME);
        }

        if (!ItemValidations::maxLength($data, 255)) {
            $this->addMessageByCode(JobProcessorErrors::MSG_JOB_NAME_TOO_LONG);
        }

    }


}