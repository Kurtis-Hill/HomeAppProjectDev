<?php
declare(strict_types=1);

namespace App\Services\Sensor\Trigger;

use App\Exceptions\Sensor\SensorTriggerConversionException;

class SensorTriggerUserInputToStringConvertor
{
    /**
     * @throws \App\Exceptions\Sensor\SensorTriggerConversionException
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
