<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorTypeReadingObjectBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingBoundaryIDDTO;
use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use JetBrains\PhpStorm\ArrayShape;

interface SensorReadingTypeObjectsBuilder
{
    #[ArrayShape([[
        Temperature::READING_TYPE => Temperature::class,
        Humidity::READING_TYPE => Humidity::class,
        Latitude::READING_TYPE => Latitude::class,
        Analog::READING_TYPE => Analog::class,
    ]])]
    public function buildReadingTypeObjectsDTO(): SensorReadingTypeObjectsDTO;

    #[ArrayShape([UpdateSensorReadingBoundaryIDDTO::class])]
    public function buildSensorIDReadingTypeUpdateDTO(SensorTypeInterface $sensorTypeObject): array;
}
