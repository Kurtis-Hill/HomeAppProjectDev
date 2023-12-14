<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class MotionReadingTypeObjectBuilder extends AbstractBoolObjectBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(Sensor $sensor): void
    {
        $sensorType = $sensor->getSensorTypeObject()::getReadingTypeName();
        if (!$sensorType instanceof MotionSensorReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }

        $motionSensor = new Motion();

        $this->setBoolDefaults(
            $sensor,
            $motionSensor,
            false,
            false
        );
    }
}
