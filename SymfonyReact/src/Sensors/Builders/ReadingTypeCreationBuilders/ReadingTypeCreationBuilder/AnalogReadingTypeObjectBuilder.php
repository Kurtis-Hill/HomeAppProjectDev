<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class AnalogReadingTypeObjectBuilder extends AbstractReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(Sensor $sensor, float|int|bool $currentReading = 10): void
    {
        $sensorType = $sensor->getSensorTypeObject();
        if (!$sensorType instanceof AnalogReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $analogSensor = new Analog();
        $analogSensor->setCurrentReading($currentReading);
        $analogSensor->setHighReading($sensorType->getMaxAnalog());
        $analogSensor->setLowReading($sensorType->getMinAnalog());
        $analogSensor->setUpdatedAt();
        $analogSensor->setSensor($sensor);

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($temperatureSensor->getReadingType());
        $readingTypeRepository->persist($analogSensor);
    }
}
