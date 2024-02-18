<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class LatitudeReadingTypeObjectBuilder extends AbstractReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(SensorTypeInterface $sensorTypeObject, float|int $currentReading = 10): void
    {
        if (!$sensorTypeObject instanceof LatitudeSensorTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $latitudeSensor = new Latitude();
        $latitudeSensor->setCurrentReading($currentReading);
        $latitudeSensor->setHighReading($sensorTypeObject->getMaxLatitude());
        $latitudeSensor->setLowReading($sensorTypeObject->getMinLatitude());
        $latitudeSensor->setUpdatedAt();
        $latitudeSensor->setSensor($sensorTypeObject->getSensor());

        $sensorTypeObject->setLatitudeObject($latitudeSensor);

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($latitudeSensor->getReadingType());
        $readingTypeRepository->persist($latitudeSensor);
    }
}
