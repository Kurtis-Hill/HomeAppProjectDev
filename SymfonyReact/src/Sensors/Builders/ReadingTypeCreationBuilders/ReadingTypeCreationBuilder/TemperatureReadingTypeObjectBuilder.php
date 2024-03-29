<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;

class TemperatureReadingTypeObjectBuilder extends AbstractReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     */
    public function buildReadingTypeObject(SensorTypeInterface $sensorTypeObject, int|float|bool $currentReading = 10): void
    {
        if (!$sensorTypeObject instanceof TemperatureReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $temperatureSensor = new Temperature();
        $temperatureSensor->setCurrentReading($currentReading);
        $temperatureSensor->setHighReading($sensorTypeObject->getMaxTemperature());
        $temperatureSensor->setLowReading($sensorTypeObject->getMinTemperature());
        $temperatureSensor->setUpdatedAt();
        $temperatureSensor->setSensor($sensorTypeObject->getSensor());

        $sensorTypeObject->setTemperature($temperatureSensor);

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($temperatureSensor->getReadingType());
        $readingTypeRepository->persist($temperatureSensor);
    }
}
