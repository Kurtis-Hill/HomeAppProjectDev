<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\Bool;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\CurrentReadingUpdateRequestBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateBoundaryReadingDTOInterface;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\Sensor\ReadingTypeObjectBuilderException;

class RelaySensorUpdateBuilder extends AbstractBoolSensorUpdateBuilder implements ReadingTypeUpdateBuilderInterface, ReadingTypeUpdateBoundaryReadingBuilderInterface, CurrentReadingUpdateRequestBuilderInterface
{
    public function buildUpdateSensorBoundaryReadingsDTO(
        SensorUpdateBoundaryDataDTOInterface $updateDataSensorBoundaryDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject
    ): UpdateBoundaryReadingDTOInterface {
        if (!$sensorReadingTypeObject instanceof Relay || !$updateDataSensorBoundaryDTO instanceof BoolSensorUpdateBoundaryDataDTO) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    $updateDataSensorBoundaryDTO->getReadingType(),
                )
            );
        }

        return $this->buildBoolUpdateSensorBoundaryReadingsDTO(
            $updateDataSensorBoundaryDTO,
            $sensorReadingTypeObject
        );
    }

    public function buildReadingTypeCurrentReadingUpdateDTO(
        AllSensorReadingTypeInterface $allSensorReadingType,
        AbstractCurrentReadingUpdateRequestDTO $sensorData,
    ): ReadingTypeUpdateCurrentReadingDTO {
        if (!$allSensorReadingType instanceof Relay) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    Relay::READING_TYPE,
                    $allSensorReadingType->getReadingType(),
                )
            );
        }

        if ($sensorData->getCurrentReading() === null) {
            throw new ReadingTypeObjectBuilderException(
                sprintf(
                    ReadingTypeObjectBuilderException::CURRENT_READING_FAILED_TO_BUILD_FOR_TYPE,
                    Relay::READING_TYPE,
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
        return $this->buildBoolRequestCurrentReadingUpdateDTO($currentReading, Relay::READING_TYPE);
    }
}
