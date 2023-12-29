<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class MotionReadingTypeReadingTypeBuilder extends AbstractBoolReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(Sensor $sensor): AllSensorReadingTypeInterface
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

        return $motionSensor;
    }
}
