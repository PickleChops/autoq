<?php

namespace Autoq\Services\JobProcessor\ItemProcessors;

use Autoq\Services\JobProcessor\Utils\ItemFilters;
use Autoq\Services\JobProcessor\Utils\ItemValidations;
use Autoq\Services\JobProcessor\JobProcessorErrors;

class JobConnectionProcessor extends JobItemProcessor
{
    protected $fieldName = 'connection';

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
            $this->addMessageByCode(JobProcessorErrors::MSG_NO_CONNECTION);
        }

        if (!ItemValidations::in($data, ['default'])) {
            $this->addMessageByCode(JobProcessorErrors::MSG_CONNECTION_NOT_DEFAULT);
        }

        if (!ItemValidations::maxLength($data, 255)) {
            $this->addMessageByCode(JobProcessorErrors::MSG_FIELD_DATA_TOO_LONG);
        }

    }
    
}