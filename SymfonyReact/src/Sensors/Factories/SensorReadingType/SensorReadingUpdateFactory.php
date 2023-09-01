<?php

namespace App\Sensors\Factories\SensorReadingType;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\Bool\MotionSensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Bool\RelaySensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard\AnalogSensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard\HumiditySensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard\LatitudeSensorUpdateBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\Standard\TemperatureSensorUpdateBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
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
