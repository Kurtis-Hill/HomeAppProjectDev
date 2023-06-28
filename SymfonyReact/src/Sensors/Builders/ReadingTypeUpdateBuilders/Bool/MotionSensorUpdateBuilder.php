<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders\Bool;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\CurrentReadingUpdateRequestBuilderInterface;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateBoundaryReadingDTOInterface;
use App\Sensors\DTO\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use Symfony\Component\Intl\Exception\NotImplementedException;

class MotionSensorUpdateBuilder extends AbstractBoolSensorUpdateBuilder implements ReadingTypeUpdateBuilderInterface, ReadingTypeUpdateBoundaryReadingBuilderInterface, CurrentReadingUpdateRequestBuilderInterface
{
    public function buildReadingTypeCurrentReadingUpdateDTO(AllSensorReadingTypeInterface $allSensorReadingType,AbstractCurrentReadingUpdateRequestDTO $sensorData) : ReadingTypeUpdateCurrentReadingDTO
    {
        throw new NotImplementedException('MotionSensorUpdateBuilder::buildReadingTypeCurrentReadingUpdateDTO');
    }

    public function buildRequestCurrentReadingUpdateDTO(mixed $currentReading) : AbstractCurrentReadingUpdateRequestDTO
    {
         throw new NotImplementedException('MotionSensorUpdateBuilder::buildRequestCurrentReadingUpdateDTO');
    }


    public function buildUpdateSensorBoundaryReadingsDTO(SensorUpdateBoundaryDataDTOInterface $updateDataSensorBoundaryDTO, AllSensorReadingTypeInterface $sensorReadingTypeObject): UpdateBoundaryReadingDTOInterface
    {
        if (!$sensorReadingTypeObject instanceof Motion || !$updateDataSensorBoundaryDTO instanceof BoolSensorUpdateBoundaryDataDTO) {
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
}
