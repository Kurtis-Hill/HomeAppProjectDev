<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use DateTimeImmutable;

class RelayReadingTypeObjectBuilder extends AbstractBoolObjectBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(Sensor $sensor, float|int|bool $currentReading = false) : void
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
    }

}
