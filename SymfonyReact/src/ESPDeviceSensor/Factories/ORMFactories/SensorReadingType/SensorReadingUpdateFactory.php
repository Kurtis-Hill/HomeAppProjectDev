<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType;

use App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders\AnalogSensorUpdateBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders\HumiditySensorUpdateBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders\LatitudeSensorUpdateBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders\TemperatureSensorUpdateBuilder;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Exceptions\SensorReadingUpdateFactoryException;

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
    public function getReadingTypeUpdateBuilder(string $readingType)
    {
        return match ($readingType) {
            Temperature::READING_TYPE => $this->temperatureSensorUpdateBuilder,
            Humidity::READING_TYPE => $this->humiditySensorUpdateBuilder,
            Latitude::READING_TYPE => $this->latitudeSensorUpdateBuilder,
            Analog::READING_TYPE => $this->analogSensorUpdateBuilder,
            default => throw new SensorReadingUpdateFactoryException(
        SensorReadingUpdateFactoryException::MESSAGE
            )
        };
    }
}
