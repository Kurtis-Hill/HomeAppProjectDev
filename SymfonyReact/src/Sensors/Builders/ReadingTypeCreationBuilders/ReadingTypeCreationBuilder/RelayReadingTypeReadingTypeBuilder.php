<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use DateTimeImmutable;

class RelayReadingTypeReadingTypeBuilder extends AbstractBoolReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(Sensor $sensor, float|int|bool $currentReading = false) : AllSensorReadingTypeInterface
    {
        $sensorType = $sensor->getSensorTypeObject()::getReadingTypeName();
        if (!$sensorType instanceof RelayReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }

        $relaySensor = new Relay();

        $this->setBoolDefaults(
            $sensor,
            $relaySensor,
            $currentReading,
            false
        );

        return $relaySensor;
    }

}
