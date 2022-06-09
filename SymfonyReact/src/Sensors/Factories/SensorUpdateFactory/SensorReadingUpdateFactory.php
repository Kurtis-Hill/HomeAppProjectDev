<?php

namespace App\Sensors\Factories\SensorUpdateFactory;

use App\Sensors\Builders\SensorUpdateBuilders\SensorReadingUpdateBuilderInterface;
use App\Sensors\Builders\SensorUpdateBuilders\StandardSensorUpdateBuilder\StandardSensorReadingUpdateBuilder;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorUpdateFactoryException;

class SensorReadingUpdateFactory
{
    private StandardSensorReadingUpdateBuilder $standardSensorUpdateBuilder;

    public function __construct(StandardSensorReadingUpdateBuilder $standardSensorUpdateBuilder)
    {
        $this->standardSensorUpdateBuilder = $standardSensorUpdateBuilder;
    }

    /**
     * @throws SensorUpdateFactoryException
     */
    public function getSensorUpdateBuilder(string $readingType): SensorReadingUpdateBuilderInterface
    {
        return match ($readingType) {
            Temperature::READING_TYPE => $this->standardSensorUpdateBuilder,
            Humidity::READING_TYPE => $this->standardSensorUpdateBuilder,
            Analog::READING_TYPE => $this->standardSensorUpdateBuilder,
            Latitude::getReadingTypeName() => $this->standardSensorUpdateBuilder,
            default => throw new SensorUpdateFactoryException(sprintf(SensorUpdateFactoryException::SENSOR_BUILDER_NOT_FOUND_SPECIFIC, $readingType))
        };
    }
}
