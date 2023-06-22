<?php

namespace App\Sensors\Builders\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;

interface ReadingTypeUpdateBuilderInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     */
    public function buildUpdateSensorBoundaryReadingsDTO(
        SensorUpdateBoundaryDataDTOInterface $updateDataSensorBoundaryDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject
    ): UpdateStandardReadingTypeBoundaryReadingsDTO;

    /**
     * @throws ReadingTypeObjectBuilderException
     * @throws ReadingTypeNotExpectedException
     */
    public function buildReadingTypeCurrentReadingUpdateDTO(
        AllSensorReadingTypeInterface $allSensorReadingType,
        AbstractCurrentReadingUpdateRequestDTO $sensorData
    ): ReadingTypeUpdateCurrentReadingDTO;

    public function buildRequestCurrentReadingUpdateDTO(mixed $currentReading): AbstractCurrentReadingUpdateRequestDTO;
}
