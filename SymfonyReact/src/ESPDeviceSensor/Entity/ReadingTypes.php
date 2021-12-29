<?php

namespace App\ESPDeviceSensor\Entity;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;

class ReadingTypes
{
    public const SENSOR_READING_TYPE_DATA = [
        Temperature::READING_TYPE => [
            'alias' => 'temp',
            'object' => Temperature::class,
        ],
        Humidity::READING_TYPE => [
            'alias' => 'humid',
            'object' => Humidity::class,
        ],
        Analog::READING_TYPE => [
            'alias' => 'analog',
            'object' => Analog::class,
        ],
        Latitude::READING_TYPE => [
            'alias' => 'lat',
            'object' => Latitude::class,
        ],
    ];
}
