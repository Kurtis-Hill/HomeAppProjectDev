<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingBoundaryIDDTO;
use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DhtSensorReadingTypeObjectsBuilder implements SensorReadingTypeObjectsBuilder
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
                Humidity::READING_TYPE => Humidity::class
            ]
        );
    }

    #[ArrayShape([UpdateSensorReadingBoundaryIDDTO::class])]
    public function buildSensorIDReadingTypeUpdateDTO(SensorTypeInterface $sensorTypeObject): array
    {
        return [
            [
                new UpdateSensorReadingBoundaryIDDTO(
                    Temperature::READING_TYPE,
                    $sensorTypeObject->getTempObject()->getSensorID(),
                )
            ],
            [
                new UpdateSensorReadingBoundaryIDDTO(
                    Humidity::READING_TYPE,
                    $sensorTypeObject->getHumidObject()->getSensorID(),
                )
            ]
        ];
    }
}
