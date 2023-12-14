<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class LatitudeReadingTypeObjectBuilder extends AbstractReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(Sensor $sensor, float|int|bool $currentReading = 10): void
    {
        $sensorType = $sensor->getSensorTypeObject();
        if (!$sensorType instanceof LatitudeReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $latitudeSensor = new Latitude();
        $latitudeSensor->setCurrentReading($currentReading);
        $latitudeSensor->setHighReading($sensorType->getMaxLatitude());
        $latitudeSensor->setLowReading($sensorType->getMinLatitude());
        $latitudeSensor->setUpdatedAt();
        $latitudeSensor->setSensor($sensor);

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($latitudeSensor->getReadingType());
        $readingTypeRepository->persist($latitudeSensor);
    }
}
