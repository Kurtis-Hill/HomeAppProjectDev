<?php

namespace App\Sensors\Factories\SensorReadingType;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard\AnalogSensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard\HumiditySensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard\LatitudeSensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard\TemperatureSensorUpdateBuilder;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
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
            Temperature::getReadingTypeName() => $this->temperatureSensorUpdateBuilder,
            Humidity::getReadingTypeName() => $this->humiditySensorUpdateBuilder,
            Latitude::getReadingTypeName() => $this->latitudeSensorUpdateBuilder,
            Analog::getReadingTypeName() => $this->analogSensorUpdateBuilder,
            default => throw new SensorReadingUpdateFactoryException(
        sprintf(SensorReadingUpdateFactoryException::MESSAGE, $readingType),
            )
        };
    }
}
