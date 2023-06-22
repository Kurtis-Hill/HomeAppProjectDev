<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class AnalogReadingTypeObjectBuilder extends AbstractReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(SensorTypeInterface $sensorTypeObject, float|int $currentReading = 10): void
    {
        if (!$sensorTypeObject instanceof AnalogReadingTypeInterface) {
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
