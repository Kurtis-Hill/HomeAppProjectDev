<?php

namespace App\Builders\Sensor\Response\ReadingTypeResponseBuilders;

use App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\BoundaryReadingTypeResponseInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

interface ReadingTypeResponseBuilderInterface
{
    public function buildReadingTypeBoundaryReadingsResponseDTO(
        AllSensorReadingTypeInterface $readingTypeObject
    ): BoundaryReadingTypeResponseInterface;
}
