<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\DTO\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;

class TemperatureSensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements ReadingTypeUpdateBuilderInterface
{
    public function buildUpdateSensorBoundaryReadingsDTO(
        SensorUpdateBoundaryDataDTOInterface $updateDataSensorBoundaryDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
    ): UpdateStandardReadingTypeBoundaryReadingsDTO {
        if (!$sensorReadingTypeObject instanceof Temperature || !$updateDataSensorBoundaryDTO instanceof StandardSensorUpdateBoundaryDataDTO) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    $updateDataSensorBoundaryDTO->getReadingType(),
                )
            );
        }

        return $this->buildStandardSensorUpdateReadingDTO(
            $updateDataSensorBoundaryDTO,
            $sensorReadingTypeObject
        );
    }

    public function buildReadingTypeCurrentReadingUpdateDTO(
        AllSensorReadingTypeInterface $allSensorReadingType,
        AbstractCurrentReadingUpdateRequestDTO $sensorData,
    ): ReadingTypeUpdateCurrentReadingDTO {
        if (!$allSensorReadingType instanceof Temperature) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    Temperature::getReadingTypeName(),
                    $allSensorReadingType->getReadingType(),
                )
            );
        }
        if (empty($sensorData->getCurrentReading())) {
            throw new ReadingTypeObjectBuilderException(
                sprintf(
                    ReadingTypeObjectBuilderException::CURRENT_READING_FAILED_TO_BUILD_FOR_TYPE,
                    Temperature::getReadingTypeName(),
                )
            );
        }

        return $this->buildStandardSensorUpdateCurrentReadingDTO(
            $allSensorReadingType,
            $sensorData->getCurrentReading()
        );
    }

    public function buildRequestCurrentReadingUpdateDTO(mixed $currentReading): AbstractCurrentReadingUpdateRequestDTO
    {
        return new TemperatureCurrentReadingUpdateRequestDTO($currentReading);
    }
}
