<?php

namespace App\Builders\Sensor\Response\ReadingTypeResponseBuilders;

use App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\BoolReadingType\BoolBoundaryReadingsTypeResponseDTO;
use App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\BoundaryReadingTypeResponseInterface;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;

class BoolReadingTypeResponseBuilder implements ReadingTypeResponseBuilderInterface
{
    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function buildReadingTypeBoundaryReadingsResponseDTO(
        AllSensorReadingTypeInterface $readingTypeObject
    ): BoundaryReadingTypeResponseInterface {
        if (!$readingTypeObject instanceof BoolReadingSensorInterface) {
            throw new ReadingTypeNotSupportedException(
                sprintf(
                    ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                    $readingTypeObject->getReadingType()
                )
            );
        }

        return new BoolBoundaryReadingsTypeResponseDTO(
            $readingTypeObject->getReadingTypeID(),
            $readingTypeObject->getReadingType(),
            $readingTypeObject->getConstRecord(),
            $readingTypeObject->getExpectedReading(),
        );
    }
}
