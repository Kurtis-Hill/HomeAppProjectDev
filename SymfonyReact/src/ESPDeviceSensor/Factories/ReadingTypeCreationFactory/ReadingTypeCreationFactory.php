<?php

namespace App\ESPDeviceSensor\Factories\ReadingTypeCreationFactory;

use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\BmpSensorReadingTypeBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\SensorReadingTypeBuilderInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;

class ReadingTypeCreationFactory
{
    private BmpSensorReadingTypeBuilder $bmpSensorReadingTypeBuilder;

    public function __construct(
        BmpSensorReadingTypeBuilder $bmpSensorReadingTypeBuilder
    ) {
        $this->bmpSensorReadingTypeBuilder = $bmpSensorReadingTypeBuilder;
    }

    public function getSensorReadingTypeBuilder(string $sensorType): SensorReadingTypeBuilderInterface
    {
        return match ($sensorType) {
            Bmp::NAME => $this->bmpSensorReadingTypeBuilder,
            default => throw new SensorTypeException(
                sprintf(
                    SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED,
                    $sensorType
                )
            )
        };
    }
}
