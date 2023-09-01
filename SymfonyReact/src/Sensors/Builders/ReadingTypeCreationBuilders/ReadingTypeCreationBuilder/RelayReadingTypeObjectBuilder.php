<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use DateTimeImmutable;

class RelayReadingTypeObjectBuilder extends AbstractBoolObjectBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(SensorTypeInterface $sensorTypeObject, float|int|bool $currentReading = false) : void
    {
        if (!$sensorTypeObject instanceof RelayReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }

        $relaySensor = new Relay();

        $this->setBoolDefaults(
            $sensorTypeObject,
            $relaySensor,
            $currentReading,
            false
        );
    }

}
