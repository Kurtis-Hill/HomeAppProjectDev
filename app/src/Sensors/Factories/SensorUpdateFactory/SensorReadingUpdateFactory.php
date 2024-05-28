<?php

namespace App\Sensors\Factories\SensorUpdateFactory;

use App\Sensors\Builders\Request\SensorUpdateBuilders\BoolSensorUpdateBuilder\BoolSensorReadingUpdateBuilder;
use App\Sensors\Builders\Request\SensorUpdateBuilders\SensorReadingUpdateBuilderInterface;
use App\Sensors\Builders\Request\SensorUpdateBuilders\StandardSensorUpdateBuilder\StandardSensorReadingUpdateBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorUpdateFactoryException;

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
