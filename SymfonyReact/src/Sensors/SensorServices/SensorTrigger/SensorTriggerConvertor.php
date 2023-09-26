<?php

namespace App\Sensors\SensorServices\SensorTrigger;

use App\Sensors\Exceptions\SensorTriggerConversionException;

class SensorTriggerConvertor
{
    /**
     * @throws SensorTriggerConversionException
     */
    public static function convertMixedToString(mixed $mixed): string
    {
        if (is_string($mixed)) {
            return $mixed;
        }

        if (is_int($mixed)) {
            return (string) $mixed;
        }

        if (is_float($mixed)) {
            return (string) $mixed;
        }

        if (is_bool($mixed)) {
            return $mixed ? 'true' : 'false';
        }

//        if (is_array($mixed)) {
//            return json_encode($mixed);
//        }
//
//        if (is_object($mixed)) {
//            return json_encode($mixed);
//        }

        throw new SensorTriggerConversionException(SensorTriggerConversionException::MESSAGE);
    }

    /**
     * @throws SensorTriggerConversionException
     */
    public static function convertStringToMixed(string $string): string|bool|null|float
    {
        if (is_numeric($string)) {
            return (float)$string;
        }

        if ($string === 'true') {
            return true;
        }

        if ($string === 'false') {
            return false;
        }

        if ($string === 'null') {
            return null;
        }

        if (is_string($string)) {
            return $string;
        }

        throw new SensorTriggerConversionException(SensorTriggerConversionException::MESSAGE);
    }
}
