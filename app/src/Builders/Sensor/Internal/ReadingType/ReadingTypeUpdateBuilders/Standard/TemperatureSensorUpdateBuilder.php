<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Standard;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\CurrentReadingUpdateRequestBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\DTOs\Sensor\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\Sensor\ReadingTypeObjectBuilderException;

class TemperatureSensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements ReadingTypeUpdateBuilderInterface, ReadingTypeUpdateBoundaryReadingBuilderInterface, CurrentReadingUpdateRequestBuilderInterface
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

        return $this->buildSensorUpdateCurrentReadingDTO(
            $allSensorReadingType,
            $sensorData->getCurrentReading()
        );
    }

    public function buildRequestCurrentReadingUpdateDTO(mixed $currentReading): AbstractCurrentReadingUpdateRequestDTO
    {
        return new TemperatureCurrentReadingUpdateRequestDTO($currentReading);
    }
}
