<?php

namespace App\Sensors\Builders\ReadingTypeResponseBuilders;

use App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType\ReadingTypeBoundaryReadingResponseInterface;
use App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType\StandardResponseReadingTypeBoundaryReadingsResponseDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;

class StandardReadingTypeResponseBuilder implements ReadingTypeResponseBuilderInterface
{
    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function buildReadingTypeBoundaryReadingsResponseDTO(
        AllSensorReadingTypeInterface $readingTypeObject
    ): ReadingTypeBoundaryReadingResponseInterface {
        if (!$readingTypeObject instanceof StandardReadingSensorInterface) {
            throw new ReadingTypeNotSupportedException(
                sprintf(
                    ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                    $readingTypeObject->getReadingType()
                )
            );
        }

        return new StandardResponseReadingTypeBoundaryReadingsResponseDTO(
            $readingTypeObject->getSensorID(),
            $readingTypeObject->getReadingType(),
            $readingTypeObject->getHighReading(),
            $readingTypeObject->getLowReading(),
            $readingTypeObject->getConstRecord(),
        );
    }
}
