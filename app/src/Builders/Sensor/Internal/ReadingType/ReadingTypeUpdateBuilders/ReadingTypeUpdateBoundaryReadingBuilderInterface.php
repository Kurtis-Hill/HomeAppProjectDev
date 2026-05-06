<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders;

use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateBoundaryReadingDTOInterface;
use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;

interface ReadingTypeUpdateBoundaryReadingBuilderInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     */
    public function buildUpdateSensorBoundaryReadingsDTO(
        SensorUpdateBoundaryDataDTOInterface $updateDataSensorBoundaryDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject
    ): UpdateBoundaryReadingDTOInterface;
}
