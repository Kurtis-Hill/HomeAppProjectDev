<?php

namespace App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Standard;

use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Sensors\Builders\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard\LatitudeResponseDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;

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
