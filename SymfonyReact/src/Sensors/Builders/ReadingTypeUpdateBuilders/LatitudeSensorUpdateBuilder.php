<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Request\CurrentReadingRequest\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\LatitudeCurrentReadingUpdateDTORequest;
use App\Sensors\DTO\Sensor\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Sensor\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;

class LatitudeSensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements ReadingTypeUpdateBuilderInterface
{
    public function buildUpdateSensorBoundaryReadingsDTO(
        array $sensorData,
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
    ): UpdateStandardReadingTypeBoundaryReadingsDTO
    {
        if (!$sensorReadingTypeObject instanceof Latitude) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    $sensorData['readingType'],
                )
            );
        }

        return $this->buildStandardSensorUpdateReadingDTO($sensorData, $sensorReadingTypeObject);
    }

    public function buildReadingTypeCurrentReadingUpdateDTO(
        AllSensorReadingTypeInterface $allSensorReadingType,
        array $sensorData
    ): ReadingTypeUpdateCurrentReadingDTO
    {
        if (!$allSensorReadingType instanceof Latitude) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    Latitude::READING_TYPE,
                    $allSensorReadingType->getReadingType(),
                )
            );
        }
        if (empty($sensorData['latitudeReading'])) {
            throw new ReadingTypeObjectBuilderException(
                sprintf(
                    ReadingTypeObjectBuilderException::CURRENT_READING_FAILED_TO_BUILD_FOR_TYPE,
                    Latitude::READING_TYPE,
                )
            );
        }

        return $this->updateStandardSensorCurrentReading(
            $allSensorReadingType,
            $sensorData['latitudeReading']
        );
    }

    public function buildRequestCurrentReadingUpdateDTO(float $currentReading): AbstractCurrentReadingUpdateRequestDTO
    {
        return new LatitudeCurrentReadingUpdateDTORequest($currentReading);
    }
}
