<?php

namespace App\Builders\Sensor\Response\ReadingTypeResponseBuilders;

use App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\BoundaryReadingTypeResponseInterface;
use App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType\StandardBoundaryReadingsTypeResponseDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;

class StandardReadingTypeResponseBuilder implements ReadingTypeResponseBuilderInterface
{
    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function buildReadingTypeBoundaryReadingsResponseDTO(
        AllSensorReadingTypeInterface $readingTypeObject
    ): BoundaryReadingTypeResponseInterface {
        if (!$readingTypeObject instanceof StandardReadingSensorInterface) {
            throw new ReadingTypeNotSupportedException(
                sprintf(
                    ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                    $readingTypeObject->getReadingType()
                )
            );
        }

        return new StandardBoundaryReadingsTypeResponseDTO(
            $readingTypeObject->getReadingTypeID(),
            $readingTypeObject->getReadingType(),
            $readingTypeObject->getHighReading(),
            $readingTypeObject->getLowReading(),
            $readingTypeObject->getConstRecord(),
        );
    }
}
