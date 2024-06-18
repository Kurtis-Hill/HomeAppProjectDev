<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Standard;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\CurrentReadingUpdateRequestBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\DTOs\Sensor\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\Sensor\ReadingTypeObjectBuilderException;

class HumiditySensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements ReadingTypeUpdateBuilderInterface, ReadingTypeUpdateBoundaryReadingBuilderInterface, CurrentReadingUpdateRequestBuilderInterface
{
    public function buildUpdateSensorBoundaryReadingsDTO(
        SensorUpdateBoundaryDataDTOInterface $updateDataSensorBoundaryDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
    ): UpdateStandardReadingTypeBoundaryReadingsDTO {
        if (!$sensorReadingTypeObject instanceof Humidity || !$updateDataSensorBoundaryDTO instanceof StandardSensorUpdateBoundaryDataDTO) {
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
        if (!$allSensorReadingType instanceof Humidity) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    Humidity::READING_TYPE,
                    $allSensorReadingType->getReadingType(),
                )
            );
        }
        if (empty($sensorData->getCurrentReading())) {
            throw new ReadingTypeObjectBuilderException(
                sprintf(
                    ReadingTypeObjectBuilderException::CURRENT_READING_FAILED_TO_BUILD_FOR_TYPE,
                    Humidity::READING_TYPE,
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
        return new HumidityCurrentReadingUpdateRequestDTO($currentReading);
    }
}
