<?php

namespace App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Bool;

use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\Bool\RelayResponseDTO;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;

class RelayResponseDTOBuilder implements SensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $readingTypeObject) : AllSensorReadingTypeResponseDTOInterface
    {
        if (!$readingTypeObject instanceof Relay) {
            throw new SensorReadingTypeObjectNotFoundException(
                SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION
            );
        }

        return new RelayResponseDTO(
            sensorResponseDTO: SensorResponseDTOBuilder::buildSensorResponseDTO($readingTypeObject->getSensor()),
            baseReadingTypeID: $readingTypeObject->getBaseReadingType()->getBaseReadingTypeID(),
            boolID: $readingTypeObject->getBoolID(),
            currentReading: $readingTypeObject->getCurrentReading(),
            requestedReading: $readingTypeObject->getRequestedReading(),
            constRecord: $readingTypeObject->getConstRecord(),
            updatedAt: $readingTypeObject->getUpdatedAt()->format('d/m/y H:i:s'),
            expectedReading: $readingTypeObject->getExpectedReading(),
        );
    }
}
