<?php

namespace App\Sensors\Factories\ORMFactories\SensorReadingType;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\AnalogSensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\HumiditySensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\LatitudeSensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\TemperatureSensorUpdateBuilder;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;

class SensorReadingUpdateFactory
{
    private TemperatureSensorUpdateBuilder $temperatureSensorUpdateBuilder;

    private HumiditySensorUpdateBuilder $humiditySensorUpdateBuilder;

    private AnalogSensorUpdateBuilder $analogSensorUpdateBuilder;

    private LatitudeSensorUpdateBuilder $latitudeSensorUpdateBuilder;

    public function __construct(
        TemperatureSensorUpdateBuilder $temperatureSensorUpdateBuilder,
        HumiditySensorUpdateBuilder $humiditySensorUpdateBuilder,
        LatitudeSensorUpdateBuilder $latitudeSensorUpdateBuilder,
        AnalogSensorUpdateBuilder $analogSensorUpdateBuilder,
    ) {
        $this->temperatureSensorUpdateBuilder = $temperatureSensorUpdateBuilder;
        $this->humiditySensorUpdateBuilder = $humiditySensorUpdateBuilder;
        $this->latitudeSensorUpdateBuilder = $latitudeSensorUpdateBuilder;
        $this->analogSensorUpdateBuilder = $analogSensorUpdateBuilder;
    }


    /**
     * @throws SensorReadingUpdateFactoryException
     */
    public function getReadingTypeUpdateBuilder(string $readingType): ReadingTypeUpdateBuilderInterface
    {
        return match ($readingType) {
            Temperature::READING_TYPE => $this->temperatureSensorUpdateBuilder,
            Humidity::READING_TYPE => $this->humiditySensorUpdateBuilder,
            Latitude::READING_TYPE => $this->latitudeSensorUpdateBuilder,
            Analog::READING_TYPE => $this->analogSensorUpdateBuilder,
            default => throw new SensorReadingUpdateFactoryException(
        sprintf(SensorReadingUpdateFactoryException::MESSAGE, $readingType),
            )
        };
    }
}
