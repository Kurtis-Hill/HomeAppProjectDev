<?php

namespace App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard;

use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\Standard\LatitudeResponseDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;

class LatitudeResponseDTOBuilder implements SensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $readingTypeObject): AllSensorReadingTypeResponseDTOInterface
    {
        if (!$readingTypeObject instanceof Latitude) {
            throw new SensorReadingTypeObjectNotFoundException(
                SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION
            );
        }
        return new LatitudeResponseDTO(
            $readingTypeObject->getReadingTypeID(),
            $readingTypeObject->getBaseReadingType()->getBaseReadingTypeID(),
            SensorResponseDTOBuilder::buildSensorResponseDTO($readingTypeObject->getSensor()),
            $readingTypeObject->getCurrentReading(),
            $readingTypeObject->getHighReading(),
            $readingTypeObject->getLowReading(),
            $readingTypeObject->getConstRecord(),
            $readingTypeObject->getUpdatedAt()->format('d/m/y H:i:s'),
        );
    }
}
