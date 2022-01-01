<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders;

use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DallasSensorReadingTypeObjectsBuilder implements SensorReadingTypeObjectsBuilder
{
    #[ArrayShape([[
        Temperature::READING_TYPE => Temperature::class,
        Humidity::READING_TYPE => Humidity::class,
        Latitude::READING_TYPE => Latitude::class,
        Analog::READING_TYPE => Analog::class,
    ]
    ])]
    #[Pure]
    public function buildReadingTypeObjectsDTO(): SensorReadingTypeObjectsDTO
    {
        return new SensorReadingTypeObjectsDTO(
            [
                Temperature::READING_TYPE => Temperature::class,
            ]
        );
    }
}
