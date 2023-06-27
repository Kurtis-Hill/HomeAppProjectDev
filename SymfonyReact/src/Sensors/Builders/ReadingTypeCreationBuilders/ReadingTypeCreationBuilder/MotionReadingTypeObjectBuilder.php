<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use DateTimeImmutable;

class MotionReadingTypeObjectBuilder extends AbstractBoolObjectBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(SensorTypeInterface $sensorTypeObject, float|int|bool $currentReading = false) : void
    {
        if (!$sensorTypeObject instanceof MotionSensorReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }

        $motionSensor = new Motion();

        $this->setBoolDefaults(
            $sensorTypeObject,
            $motionSensor,
            $currentReading,
            false
        );
    }
}
