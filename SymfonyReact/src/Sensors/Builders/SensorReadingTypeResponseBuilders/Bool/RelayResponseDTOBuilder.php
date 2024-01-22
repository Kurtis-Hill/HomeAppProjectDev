<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders\Bool;

use App\Sensors\Builders\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool\RelayResponseDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;

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
