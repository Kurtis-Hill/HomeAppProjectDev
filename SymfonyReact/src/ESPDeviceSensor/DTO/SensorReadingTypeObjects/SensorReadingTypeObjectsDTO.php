<?php

namespace App\ESPDeviceSensor\DTO\SensorReadingTypeObjects;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use JetBrains\PhpStorm\ArrayShape;

class SensorReadingTypeObjectsDTO
{
    private array $sensorReadingTypeObjects;

    public function __construct(array $sensorReadingTypeObjects)
    {
        $this->sensorReadingTypeObjects = $sensorReadingTypeObjects;
    }

    #[ArrayShape([Temperature::class | Humidity::class | Analog::class | Latitude::class | Soil::class])]
    public function getSensorReadingTypeObjects(): array
    {
        return $this->sensorReadingTypeObjects;
    }

}
