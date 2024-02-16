<?php

namespace App\Sensors\Builders\Response\ReadingTypeResponseBuilders;

use App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\BoolReadingType\BoolBoundaryReadingsTypeResponseDTO;
use App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\BoundaryReadingTypeResponseInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;

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
