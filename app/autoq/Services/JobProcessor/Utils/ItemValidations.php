<?php

namespace Autoq\Services\JobProcessor\Utils;


class ItemValidations
{
    static function exists($value)
    {
        return $value != "";
    }

    static function maxLength($value, $max)
    {
        return mb_strlen($value) <= $max;
    }

    static function in($needle, $haystack)
    {
        return in_array($needle, $haystack);
    }
}