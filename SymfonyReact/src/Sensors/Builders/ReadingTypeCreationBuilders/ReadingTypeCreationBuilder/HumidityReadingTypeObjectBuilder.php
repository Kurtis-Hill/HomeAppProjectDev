<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class HumidityReadingTypeObjectBuilder extends AbstractReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(SensorTypeInterface $sensorTypeObject, int|float $currentReading = 10): void
    {
        if (!$sensorTypeObject instanceof HumidityReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $humiditySensor = new Humidity();
        $humiditySensor->setCurrentReading($currentReading);
        $humiditySensor->setHighReading($sensorTypeObject->getMaxHumidity());
        $humiditySensor->setLowReading($sensorTypeObject->getMinHumidity());
        $humiditySensor->setUpdatedAt();
        $humiditySensor->setSensor($sensorTypeObject->getSensor());

        $sensorTypeObject->setHumidObject($humiditySensor);

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($humiditySensor->getReadingType());
        $readingTypeRepository->persist($humiditySensor);
    }
}
