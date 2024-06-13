<?php

namespace App\Factories\Sensor\SensorUpdateFactory;

use App\Builders\Sensor\Request\SensorUpdateBuilders\BoolSensorUpdateBuilder\BoolSensorReadingUpdateBuilder;
use App\Builders\Sensor\Request\SensorUpdateBuilders\SensorReadingUpdateBuilderInterface;
use App\Builders\Sensor\Request\SensorUpdateBuilders\StandardSensorUpdateBuilder\StandardSensorReadingUpdateBuilder;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\SensorUpdateFactoryException;

class SensorReadingUpdateFactory
{
    private StandardSensorReadingUpdateBuilder $standardSensorUpdateBuilder;

    private BoolSensorReadingUpdateBuilder $boolSensorUpdateBuilder;

    public function __construct(
        StandardSensorReadingUpdateBuilder $standardSensorUpdateBuilder,
        BoolSensorReadingUpdateBuilder $boolSensorUpdateBuilder
    ) {
        $this->standardSensorUpdateBuilder = $standardSensorUpdateBuilder;
        $this->boolSensorUpdateBuilder = $boolSensorUpdateBuilder;
    }

    /**
     * @throws SensorUpdateFactoryException
     */
    public function getSensorUpdateBuilder(string $readingType): SensorReadingUpdateBuilderInterface
    {
        return match ($readingType) {
            Temperature::getReadingTypeName(),
            Humidity::getReadingTypeName(),
            Analog::getReadingTypeName(),
            Latitude::getReadingTypeName() => $this->standardSensorUpdateBuilder,
            Motion::getReadingTypeName(),
            Relay::getReadingTypeName() => $this->boolSensorUpdateBuilder,
            default => throw new SensorUpdateFactoryException(sprintf(SensorUpdateFactoryException::SENSOR_BUILDER_NOT_FOUND_SPECIFIC, $readingType))
        };
    }
}
