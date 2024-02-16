<?php

namespace App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Bool;

use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Sensors\Builders\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool\MotionResponseDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;

class MotionResponseDTOBuilder implements SensorResponseDTOBuilderInterface
{
    /**
     * @throws SensorReadingTypeObjectNotFoundException
     */
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $readingTypeObject) : AllSensorReadingTypeResponseDTOInterface
    {
        if (!$readingTypeObject instanceof Motion) {
            throw new SensorReadingTypeObjectNotFoundException(
                SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION
            );
        }

        return new MotionResponseDTO(
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
