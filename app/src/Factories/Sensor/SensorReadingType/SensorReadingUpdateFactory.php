<?php

namespace App\Factories\Sensor\SensorReadingType;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Bool\MotionSensorUpdateBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Bool\RelaySensorUpdateBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Standard\AnalogSensorUpdateBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Standard\HumiditySensorUpdateBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Standard\LatitudeSensorUpdateBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Standard\TemperatureSensorUpdateBuilder;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\SensorReadingUpdateFactoryException;

class SensorReadingUpdateFactory
{
    private TemperatureSensorUpdateBuilder $temperatureSensorUpdateBuilder;

    private HumiditySensorUpdateBuilder $humiditySensorUpdateBuilder;

    private AnalogSensorUpdateBuilder $analogSensorUpdateBuilder;

    private LatitudeSensorUpdateBuilder $latitudeSensorUpdateBuilder;

    private MotionSensorUpdateBuilder $motionSensorUpdateBuilder;

    private RelaySensorUpdateBuilder $relaySensorUpdateBuilder;

    public function __construct(
        TemperatureSensorUpdateBuilder $temperatureSensorUpdateBuilder,
        HumiditySensorUpdateBuilder $humiditySensorUpdateBuilder,
        LatitudeSensorUpdateBuilder $latitudeSensorUpdateBuilder,
        AnalogSensorUpdateBuilder $analogSensorUpdateBuilder,
        MotionSensorUpdateBuilder $motionSensorUpdateBuilder,
        RelaySensorUpdateBuilder $relaySensorUpdateBuilder
    ) {
        $this->temperatureSensorUpdateBuilder = $temperatureSensorUpdateBuilder;
        $this->humiditySensorUpdateBuilder = $humiditySensorUpdateBuilder;
        $this->latitudeSensorUpdateBuilder = $latitudeSensorUpdateBuilder;
        $this->analogSensorUpdateBuilder = $analogSensorUpdateBuilder;
        $this->motionSensorUpdateBuilder = $motionSensorUpdateBuilder;
        $this->relaySensorUpdateBuilder = $relaySensorUpdateBuilder;
    }


    /**
     * @throws SensorReadingUpdateFactoryException
     */
    public function getReadingTypeUpdateBuilder(string $readingType): ReadingTypeUpdateBoundaryReadingBuilderInterface
    {
        return match ($readingType) {
            Temperature::getReadingTypeName() => $this->temperatureSensorUpdateBuilder,
            Humidity::getReadingTypeName() => $this->humiditySensorUpdateBuilder,
            Latitude::getReadingTypeName() => $this->latitudeSensorUpdateBuilder,
            Analog::getReadingTypeName() => $this->analogSensorUpdateBuilder,
            Motion::getReadingTypeName() => $this->motionSensorUpdateBuilder,
            Relay::getReadingTypeName() => $this->relaySensorUpdateBuilder,
            default => throw new SensorReadingUpdateFactoryException(
        sprintf(SensorReadingUpdateFactoryException::MESSAGE, $readingType),
            )
        };
    }
}
