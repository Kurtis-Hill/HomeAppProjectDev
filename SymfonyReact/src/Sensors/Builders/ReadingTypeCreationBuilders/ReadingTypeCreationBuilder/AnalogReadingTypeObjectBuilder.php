<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class AnalogReadingTypeObjectBuilder extends AbstractReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(SensorTypeInterface $sensorTypeObject, float|int $currentReading = 10): void
    {
        if (!$sensorTypeObject instanceof AnalogSensorTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $analogSensor = new Analog();
        $analogSensor->setCurrentReading($currentReading);
        $analogSensor->setHighReading($sensorTypeObject->getMaxAnalog());
        $analogSensor->setLowReading($sensorTypeObject->getMinAnalog());
        $analogSensor->setUpdatedAt();
        $analogSensor->setSensor($sensorTypeObject->getSensor());

        $sensorTypeObject->setAnalogObject($analogSensor);
    }
}
